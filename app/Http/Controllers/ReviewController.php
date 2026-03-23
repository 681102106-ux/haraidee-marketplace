<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        if (Auth::id() !== $transaction->user_id) abort(403);
        if ($transaction->payment_status !== 'completed') abort(403, 'ต้องซื้อขายให้สำเร็จก่อนถึงจะรีวิวได้ครับ');
        
        if ($transaction->review()->exists()) {
            return back()->withErrors(['error' => 'คุณได้รีวิวการซื้อขายนี้ไปแล้วครับ']);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        Review::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $transaction->product->user_id, 
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', '⭐ ขอบคุณสำหรับรีวิวครับ! คะแนนของคุณช่วยให้คอมมูนิตี้น่าอยู่ขึ้น');
    }
}