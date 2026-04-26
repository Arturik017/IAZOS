<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\SellerStory;
use App\Models\User;
use App\Support\MessageState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryInteractionController extends Controller
{
    public function message(Request $request, SellerStory $story): JsonResponse
    {
        $viewer = $request->user();
        abort_unless($viewer, 403);
        abort_if((int) $viewer->id === (int) $story->seller_id, 422, 'Nu iti poti trimite tie insuti un mesaj din story.');
        abort_unless(User::supportsMessaging(), 409, 'Mesageria nu este pregatita.');

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $conversation = Conversation::query()
            ->where('type', 'seller_client')
            ->where('seller_id', $story->seller_id)
            ->where('client_id', $viewer->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'seller_client',
                'seller_id' => $story->seller_id,
                'client_id' => $viewer->id,
                'created_by' => $viewer->id,
                'product_id' => null,
                'last_message_at' => now(),
            ]);
        }

        $payload = [
            'conversation_id' => $conversation->id,
            'sender_id' => $viewer->id,
            'body' => trim((string) $data['body']),
        ];

        if (MessageState::supportsThreadingAndStoryContext()) {
            $payload['seller_story_id'] = $story->id;
        }

        ConversationMessage::create($payload);

        $conversation->forceFill([
            'last_message_at' => $conversation->messages()->max('created_at'),
        ])->save();

        return response()->json([
            'ok' => true,
            'message' => 'Mesajul a fost trimis sellerului.',
            'conversation_url' => route('messages.show', $conversation),
        ]);
    }

    public function like(Request $request, SellerStory $story): JsonResponse
    {
        $viewer = $request->user();
        abort_unless($viewer, 403);
        abort_unless(User::supportsSellerStoryLikes(), 409, 'Like-urile pentru story nu sunt pregatite.');

        $story->likes()->syncWithoutDetaching([$viewer->id]);

        return response()->json([
            'ok' => true,
            'liked' => true,
            'likes_count' => $story->likes()->count(),
        ]);
    }

    public function unlike(Request $request, SellerStory $story): JsonResponse
    {
        $viewer = $request->user();
        abort_unless($viewer, 403);
        abort_unless(User::supportsSellerStoryLikes(), 409, 'Like-urile pentru story nu sunt pregatite.');

        $story->likes()->detach($viewer->id);

        return response()->json([
            'ok' => true,
            'liked' => false,
            'likes_count' => $story->likes()->count(),
        ]);
    }
}
