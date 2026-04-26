<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Product;
use App\Models\SellerStory;
use App\Models\User;
use App\Support\ImageStorage;
use App\Support\MediaUrl;
use App\Support\MessageState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $user = $request->user();

        $conversations = Conversation::query()
            ->with([
                'seller.sellerProfile',
                'client.sellerProfile',
                'admin.sellerProfile',
                'product',
                'latestMessage.sender.sellerProfile',
            ])
            ->withCount([
                'messages as unread_messages_count' => function ($query) use ($user) {
                    $query->whereNull('read_at')
                        ->where('sender_id', '!=', $user->id);
                },
            ])
            ->where(function ($query) use ($user) {
                $query->where('seller_id', $user->id)
                    ->orWhere('client_id', $user->id)
                    ->orWhere('admin_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get()
            ->unique(function (Conversation $conversation) {
                if ($conversation->type === 'seller_client') {
                    return implode(':', [
                        $conversation->type,
                        $conversation->seller_id,
                        $conversation->client_id,
                    ]);
                }

                return implode(':', [
                    $conversation->type,
                    $conversation->seller_id,
                    $conversation->admin_id,
                ]);
            })
            ->values();

        return response()->json([
            'ok' => true,
            'unread_count' => MessageState::unreadCount(),
            'conversations' => $conversations->map(fn (Conversation $conversation) => $this->conversationPayload($conversation, $user))->values(),
        ]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $user = $request->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);

        $conversation->load([
            'seller.sellerProfile',
            'client.sellerProfile',
            'admin.sellerProfile',
            'product',
            'messages' => fn ($query) => $query->with('sender.sellerProfile')
                ->with('replyTo.sender.sellerProfile')
                ->with('story')
                ->orderBy('created_at')
                ->orderBy('id'),
        ]);

        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);

        return response()->json([
            'ok' => true,
            'conversation' => $this->conversationPayload($conversation, $user, true),
        ]);
    }

    public function startSeller(Request $request, User $user)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $authUser = $request->user();

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        abort_if((int) $authUser->id === (int) $user->id, 422, 'You cannot message yourself.');
        abort_if(($authUser->role ?? null) === 'seller', 422, 'Seller should use admin thread.');

        $productId = $request->integer('product_id') ?: null;
        if ($productId) {
            $product = Product::query()
                ->where('id', $productId)
                ->where('seller_id', $user->id)
                ->first();

            if (!$product) {
                $productId = null;
            }
        }

        if (($authUser->role ?? null) === 'admin') {
            $conversation = Conversation::firstOrCreate([
                'type' => 'seller_admin',
                'seller_id' => $user->id,
                'admin_id' => $authUser->id,
            ], [
                'created_by' => $authUser->id,
                'last_message_at' => now(),
            ]);
        } else {
            $conversation = Conversation::query()
                ->where('type', 'seller_client')
                ->where('seller_id', $user->id)
                ->where('client_id', $authUser->id)
                ->orderByDesc('last_message_at')
                ->orderByDesc('id')
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'type' => 'seller_client',
                    'seller_id' => $user->id,
                    'client_id' => $authUser->id,
                    'product_id' => $productId,
                    'created_by' => $authUser->id,
                    'last_message_at' => now(),
                ]);
            } elseif ($productId && (int) $conversation->product_id !== (int) $productId) {
                $conversation->forceFill([
                    'product_id' => $productId,
                ])->save();
            }
        }

        $conversation->load([
            'seller.sellerProfile',
            'client.sellerProfile',
            'admin.sellerProfile',
            'product',
            'messages' => fn ($query) => $query->with('sender.sellerProfile')
                ->with('replyTo.sender.sellerProfile')
                ->with('story')
                ->orderBy('created_at')
                ->orderBy('id'),
        ]);

        return response()->json([
            'ok' => true,
            'conversation' => $this->conversationPayload($conversation, $authUser, true),
        ]);
    }

    public function startAdmin(Request $request)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $authUser = $request->user();
        abort_if(($authUser->role ?? null) !== 'seller', 422, 'Only sellers can start admin threads.');

        $admin = User::query()->where('role', 'admin')->orderBy('id')->first();
        abort_if(!$admin, 422, 'No admin available.');

        $conversation = Conversation::firstOrCreate([
            'type' => 'seller_admin',
            'seller_id' => $authUser->id,
            'admin_id' => $admin->id,
        ], [
            'created_by' => $authUser->id,
            'last_message_at' => now(),
        ]);

        $conversation->load([
            'seller.sellerProfile',
            'client.sellerProfile',
            'admin.sellerProfile',
            'product',
            'messages' => fn ($query) => $query->with('sender.sellerProfile')
                ->with('replyTo.sender.sellerProfile')
                ->with('story')
                ->orderBy('created_at')
                ->orderBy('id'),
        ]);

        return response()->json([
            'ok' => true,
            'conversation' => $this->conversationPayload($conversation, $authUser, true),
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $user = $request->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,bmp,avif', 'max:12288'],
            'reply_to_message_id' => ['nullable', 'integer'],
            'seller_story_id' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image') && !MessageState::supportsMessageMedia()) {
            return response()->json([
                'ok' => false,
                'message' => 'Messaging media columns are not ready. Run the latest migration.',
            ], 422);
        }

        if (($request->filled('reply_to_message_id') || $request->filled('seller_story_id')) && !MessageState::supportsThreadingAndStoryContext()) {
            return response()->json([
                'ok' => false,
                'message' => 'Reply and story context columns are not ready. Run the latest migration.',
            ], 422);
        }

        $body = trim((string) ($data['body'] ?? ''));
        $imagePath = $request->hasFile('image')
            ? ImageStorage::storeWebp($request->file('image'), 'messages/images', 'public', 82, 'image', 1600, 1600)
            : null;
        $replyToMessageId = null;
        $sellerStoryId = null;

        if ($body === '' && !$imagePath) {
            return response()->json([
                'ok' => false,
                'message' => 'Message must contain text or image.',
            ], 422);
        }

        if (MessageState::supportsThreadingAndStoryContext()) {
            if (!empty($data['reply_to_message_id'])) {
                $replyMessage = ConversationMessage::query()
                    ->where('id', $data['reply_to_message_id'])
                    ->where('conversation_id', $conversation->id)
                    ->first();

                if (!$replyMessage) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Reply target not found in this conversation.',
                    ], 422);
                }

                $replyToMessageId = $replyMessage->id;
            }

            if (!empty($data['seller_story_id'])) {
                $story = SellerStory::query()->find($data['seller_story_id']);

                if (!$story || (int) $story->seller_id !== (int) $conversation->seller_id) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Story is no longer available for this seller.',
                    ], 422);
                }

                $sellerStoryId = $story->id;
            }
        }

        $payload = [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $body !== '' ? $body : null,
        ];

        if (MessageState::supportsMessageMedia()) {
            $payload['image_path'] = $imagePath;
        }

        if (MessageState::supportsThreadingAndStoryContext()) {
            $payload['reply_to_message_id'] = $replyToMessageId;
            $payload['seller_story_id'] = $sellerStoryId;
        }

        $message = ConversationMessage::create($payload);

        $this->refreshConversationTimestamp($conversation);

        $message->load('sender.sellerProfile');

        return response()->json([
            'ok' => true,
            'message' => $this->messagePayload($message),
        ]);
    }

    public function update(Request $request, Conversation $conversation, ConversationMessage $message)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $user = $request->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);
        abort_unless((int) $message->conversation_id === (int) $conversation->id, 404);
        abort_unless((int) $message->sender_id === (int) $user->id, 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,bmp,avif', 'max:12288'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        if (($request->hasFile('image') || (bool) ($data['remove_image'] ?? false)) && !MessageState::supportsMessageMedia()) {
            return response()->json([
                'ok' => false,
                'message' => 'Messaging media columns are not ready. Run the latest migration.',
            ], 422);
        }

        $body = trim((string) ($data['body'] ?? ''));
        $removeImage = (bool) ($data['remove_image'] ?? false);
        $imagePath = MessageState::supportsMessageMedia() ? $message->image_path : null;

        if ($removeImage && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = ImageStorage::storeWebp($request->file('image'), 'messages/images', 'public', 82, 'image', 1600, 1600);
        }

        if ($body === '' && !$imagePath) {
            return response()->json([
                'ok' => false,
                'message' => 'Message must contain text or image.',
            ], 422);
        }

        $payload = [
            'body' => $body !== '' ? $body : null,
        ];

        if (MessageState::supportsMessageMedia()) {
            $payload['image_path'] = $imagePath;
            $payload['edited_at'] = now();
        }

        $message->update($payload);

        $message->load('sender.sellerProfile');
        $this->refreshConversationTimestamp($conversation);

        return response()->json([
            'ok' => true,
            'message' => $this->messagePayload($message),
        ]);
    }

    public function destroy(Request $request, Conversation $conversation, ConversationMessage $message)
    {
        abort_unless(MessageState::supported(), 503, 'Messaging is not ready.');

        $user = $request->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);
        abort_unless((int) $message->conversation_id === (int) $conversation->id, 404);
        abort_unless((int) $message->sender_id === (int) $user->id, 403);

        if (MessageState::supportsMessageMedia() && $message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();
        $this->refreshConversationTimestamp($conversation);

        return response()->json([
            'ok' => true,
        ]);
    }

    private function conversationPayload(Conversation $conversation, User $viewer, bool $includeMessages = false): array
    {
        $other = $conversation->otherParticipantFor($viewer);

        $payload = [
            'id' => $conversation->id,
            'type' => $conversation->type,
            'product' => $conversation->product ? [
                'id' => $conversation->product->id,
                'name' => $conversation->product->name,
            ] : null,
            'other_participant' => $other ? [
                'id' => $other->id,
                'name' => $other->sellerProfile->shop_name ?? $other->name,
                'avatar_url' => MediaUrl::public($other->sellerProfile->avatar_path ?? null),
                'role' => $other->role,
            ] : null,
            'last_message_at' => $conversation->last_message_at?->toDateTimeString(),
            'unread_messages_count' => (int) ($conversation->unread_messages_count ?? 0),
            'latest_message' => $conversation->latestMessage ? $this->messagePayload($conversation->latestMessage) : null,
        ];

        if ($includeMessages) {
            $payload['messages'] = $conversation->messages
                ->sortBy('id')
                ->values()
                ->map(fn (ConversationMessage $message) => $this->messagePayload($message))
                ->all();
        }

        return $payload;
    }

    private function messagePayload(ConversationMessage $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'image_url' => MessageState::supportsMessageMedia() ? MediaUrl::public($message->image_path) : null,
            'reply_to' => MessageState::supportsThreadingAndStoryContext() && $message->replyTo ? [
                'id' => $message->replyTo->id,
                'body' => $message->replyTo->body,
                'sender_name' => $message->replyTo->sender->sellerProfile->shop_name ?? $message->replyTo->sender->name,
            ] : null,
            'story' => MessageState::supportsThreadingAndStoryContext() && $message->story ? [
                'id' => $message->story->id,
                'media_type' => $message->story->media_type,
                'thumbnail_url' => MediaUrl::public($message->story->thumbnail_path ?: $message->story->media_path),
                'caption' => $message->story->caption,
            ] : null,
            'read_at' => $message->read_at?->toDateTimeString(),
            'edited_at' => MessageState::supportsMessageMedia() ? $message->edited_at?->toDateTimeString() : null,
            'created_at' => $message->created_at?->toDateTimeString(),
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->sellerProfile->shop_name ?? $message->sender->name,
                'avatar_url' => MediaUrl::public($message->sender->sellerProfile->avatar_path ?? null),
                'role' => $message->sender->role,
            ],
        ];
    }

    private function refreshConversationTimestamp(Conversation $conversation): void
    {
        $latestMessageAt = $conversation->messages()->max('created_at');

        $conversation->forceFill([
            'last_message_at' => $latestMessageAt,
        ])->save();
    }
}
