<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Product;
use App\Models\SellerStory;
use App\Models\User;
use App\Support\ImageStorage;
use App\Support\MessageState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->route('home')->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $user = auth()->user();
        $conversations = $this->conversationListFor($user);

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
            'messageStoryGroups' => $this->messageStoryGroups($conversations),
        ]);
    }

    public function show(Conversation $conversation): View|RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->route('home')->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $user = auth()->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);

        $activeConversation = $this->loadConversation($conversation);
        $this->attachStoryMeta($activeConversation, $user);
        $this->markConversationAsRead($activeConversation, $user);
        $conversations = $this->conversationListFor($user);

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $activeConversation->messages,
            'messageStoryGroups' => $this->messageStoryGroups($conversations, $activeConversation),
        ]);
    }

    public function startSeller(Request $request, User $user): RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->back()->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $authUser = auth()->user();

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        if ((int) $authUser->id === (int) $user->id) {
            return redirect()->back()->with('error', 'Nu iti poti trimite mesaje tie insuti.');
        }

        if (($authUser->role ?? null) === 'seller') {
            return redirect()->back()->with('error', 'Sellerii folosesc conversatia cu adminul din inbox.');
        }

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

        return redirect()->route('messages.show', $conversation);
    }

    public function startAdmin(): RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->back()->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $authUser = auth()->user();

        if (($authUser->role ?? null) !== 'seller') {
            return redirect()->back()->with('error', 'Conversatia cu adminul este disponibila pentru selleri.');
        }

        $admin = User::query()->where('role', 'admin')->orderBy('id')->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'Nu exista momentan un admin disponibil.');
        }

        $conversation = Conversation::firstOrCreate([
            'type' => 'seller_admin',
            'seller_id' => $authUser->id,
            'admin_id' => $admin->id,
        ], [
            'created_by' => $authUser->id,
            'last_message_at' => now(),
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->back()->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $user = auth()->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,bmp,avif', 'max:12288'],
            'reply_to_message_id' => ['nullable', 'integer'],
            'seller_story_id' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image') && !MessageState::supportsMessageMedia()) {
            return redirect()->route('messages.show', $conversation)->with('error', 'Pentru imagini in mesaje trebuie rulata migrarea noua.');
        }

        if (($request->filled('reply_to_message_id') || $request->filled('seller_story_id')) && !MessageState::supportsThreadingAndStoryContext()) {
            return redirect()->route('messages.show', $conversation)->with('error', 'Pentru reply si context story trebuie rulata migrarea noua.');
        }

        $body = trim((string) ($data['body'] ?? ''));
        $imagePath = $request->hasFile('image')
            ? ImageStorage::storeWebp($request->file('image'), 'messages/images', 'public', 82, 'image', 1600, 1600)
            : null;
        $replyToMessageId = null;
        $sellerStoryId = null;

        if ($body === '' && !$imagePath) {
            return redirect()->route('messages.show', $conversation)->with('error', 'Mesajul trebuie sa contina text sau imagine.');
        }

        if (MessageState::supportsThreadingAndStoryContext()) {
            if (!empty($data['reply_to_message_id'])) {
                $replyMessage = ConversationMessage::query()
                    ->where('id', $data['reply_to_message_id'])
                    ->where('conversation_id', $conversation->id)
                    ->first();

                if (!$replyMessage) {
                    return redirect()->route('messages.show', $conversation)->with('error', 'Mesajul la care raspunzi nu mai exista.');
                }

                $replyToMessageId = $replyMessage->id;
            }

            if (!empty($data['seller_story_id'])) {
                $story = SellerStory::query()->find($data['seller_story_id']);

                if (!$story || (int) $story->seller_id !== (int) $conversation->seller_id) {
                    return redirect()->route('messages.show', $conversation)->with('error', 'Story-ul selectat nu mai este disponibil.');
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

        ConversationMessage::create($payload);

        $this->refreshConversationTimestamp($conversation);

        return redirect()->route('messages.show', $conversation)->with('success', 'Mesajul a fost trimis.');
    }

    public function update(Request $request, Conversation $conversation, ConversationMessage $message): RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->back()->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $user = auth()->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);
        abort_unless((int) $message->conversation_id === (int) $conversation->id, 404);
        abort_unless((int) $message->sender_id === (int) $user->id, 403);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,bmp,avif', 'max:12288'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        if (($request->hasFile('image') || (bool) ($data['remove_image'] ?? false)) && !MessageState::supportsMessageMedia()) {
            return redirect()->route('messages.show', $conversation)->with('error', 'Pentru editarea imaginilor in mesaje trebuie rulata migrarea noua.');
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
            return redirect()->route('messages.show', $conversation)->with('error', 'Mesajul trebuie sa contina text sau imagine.');
        }

        $payload = [
            'body' => $body !== '' ? $body : null,
        ];

        if (MessageState::supportsMessageMedia()) {
            $payload['image_path'] = $imagePath;
            $payload['edited_at'] = now();
        }

        $message->update($payload);

        $this->refreshConversationTimestamp($conversation);

        return redirect()->route('messages.show', $conversation)->with('success', 'Mesajul a fost actualizat.');
    }

    public function destroy(Conversation $conversation, ConversationMessage $message): RedirectResponse
    {
        if (!MessageState::supported()) {
            return redirect()->back()->with('error', 'Mesageria nu este inca pregatita. Ruleaza migrarile noi.');
        }

        $user = auth()->user();
        abort_unless($conversation->includesUser((int) $user->id), 403);
        abort_unless((int) $message->conversation_id === (int) $conversation->id, 404);
        abort_unless((int) $message->sender_id === (int) $user->id, 403);

        if (MessageState::supportsMessageMedia() && $message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();
        $this->refreshConversationTimestamp($conversation);

        return redirect()->route('messages.show', $conversation)->with('success', 'Mesajul a fost sters definitiv.');
    }

    private function conversationListFor(User $user)
    {
        return Conversation::query()
            ->with([
                'seller.sellerProfile',
                'client.sellerProfile',
                'admin.sellerProfile',
                'product',
                'latestMessage.sender',
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
            ->values()
            ->each(fn (Conversation $conversation) => $this->attachStoryMeta($conversation, $user));
    }

    private function loadConversation(Conversation $conversation): Conversation
    {
        return $conversation->load([
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
    }

    private function markConversationAsRead(Conversation $conversation, User $user): void
    {
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update(['read_at' => now()]);
    }

    private function refreshConversationTimestamp(Conversation $conversation): void
    {
        $latestMessageAt = $conversation->messages()->max('created_at');

        $conversation->forceFill([
            'last_message_at' => $latestMessageAt,
        ])->save();
    }

    private function attachStoryMeta(Conversation $conversation, User $viewer): void
    {
        $other = $conversation->otherParticipantFor($viewer);

        if (!$other || !User::supportsSellerStories() || ($other->role ?? null) !== 'seller') {
            $conversation->other_active_story_ids = [];
            $conversation->other_has_active_stories = false;
            return;
        }

        $storyIds = SellerStory::query()
            ->where('seller_id', $other->id)
            ->active()
            ->latest()
            ->pluck('id')
            ->values()
            ->all();

        $conversation->other_active_story_ids = $storyIds;
        $conversation->other_has_active_stories = count($storyIds) > 0;
    }

    private function messageStoryGroups($conversations, ?Conversation $activeConversation = null)
    {
        if (!User::supportsSellerStories()) {
            return collect();
        }

        $sellerIds = $conversations
            ->map(function (Conversation $conversation) {
                if (($conversation->seller?->role ?? null) === 'seller') {
                    return (int) $conversation->seller_id;
                }

                if (($conversation->otherParticipantFor(auth()->user())?->role ?? null) === 'seller') {
                    return (int) $conversation->otherParticipantFor(auth()->user())->id;
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values();

        if ($activeConversation && ($activeConversation->seller?->role ?? null) === 'seller') {
            $sellerIds->push((int) $activeConversation->seller_id);
            $sellerIds = $sellerIds->unique()->values();
        }

        if ($sellerIds->isEmpty()) {
            return collect();
        }

        $likedStoryIds = collect();
        if (User::supportsSellerStoryLikes() && auth()->check()) {
            $likedStoryIds = auth()->user()->likedStories()->pluck('seller_stories.id');
        }

        $storiesQuery = SellerStory::query()
            ->with(['seller.sellerProfile'])
            ->active()
            ->whereIn('seller_id', $sellerIds);

        if (User::supportsSellerStoryLikes()) {
            $storiesQuery->withCount('likes');
        }

        $stories = $storiesQuery
            ->latest()
            ->get()
            ->groupBy('seller_id')
            ->map(function ($items) use ($likedStoryIds) {
                $seller = $items->first()->seller;

                return [
                    'seller_id' => $seller->id,
                    'seller_name' => $seller->sellerProfile->shop_name ?? $seller->name,
                    'seller_avatar' => \App\Support\MediaUrl::public($seller->sellerProfile->avatar_path ?? null),
                    'seller_url' => route('seller.public.show', $seller),
                    'stories' => $items->map(function (SellerStory $story) use ($likedStoryIds) {
                        return [
                            'id' => $story->id,
                            'media_type' => $story->media_type,
                            'media_url' => \App\Support\MediaUrl::public($story->media_path),
                            'thumbnail_url' => \App\Support\MediaUrl::public($story->thumbnail_path ?: $story->media_path),
                            'caption' => $story->caption,
                            'expires_at' => $story->expires_at?->format('d.m H:i'),
                            'likes_count' => (int) ($story->likes_count ?? 0),
                            'is_liked' => $likedStoryIds->contains($story->id),
                        ];
                    })->values()->all(),
                ];
            })
            ->values();

        return $stories;
    }
}
