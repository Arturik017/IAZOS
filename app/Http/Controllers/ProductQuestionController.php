<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductQuestionController extends Controller
{
    // CREATE QUESTION (deja ai)
    public function store(Request $request, Product $product)
    {
        try {
            $rules = [
                'question' => ['required', 'string', 'max:2000'],
            ];

            if (!auth()->check()) {
                $rules['guest_name'] = ['required', 'string', 'max:255'];
            }

            $data = $request->validate($rules);

            $product->questions()->create([
                'user_id' => auth()->id(),
                'guest_name' => auth()->check() ? null : $data['guest_name'],
                'guest_email' => null,
                'question' => $data['question'],
            ]);

            return back()->with('success', 'Întrebarea a fost trimisă.');
        } catch (\Throwable $e) {
            Log::error('Product question store failed', [
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Eroare la trimitere.');
        }
    }

    // 🔥 ANSWER (IMPORTANT)
    public function answer(Request $request, ProductQuestion $question)
    {
        try {
            $request->validate([
                'answer' => ['required', 'string', 'max:3000'],
            ]);

            // 🔒 verificare: doar sellerul produsului sau admin
            $product = $question->product;

            $isOwner = auth()->id() === $product->seller_id;
            $isAdmin = auth()->user()?->role === 'admin';

            if (!$isOwner && !$isAdmin) {
                abort(403);
            }

            $question->update([
                'answer' => $request->answer,
                'answered_by' => auth()->id(),
                'answered_at' => now(),
            ]);

            return back()->with('success', 'Răspuns salvat.');
        } catch (\Throwable $e) {
            Log::error('Answer failed', [
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Eroare la răspuns.');
        }
    }
}