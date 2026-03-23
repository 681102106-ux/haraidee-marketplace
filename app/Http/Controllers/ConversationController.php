<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewMessageNotification;
use App\Models\Message;

class ConversationController extends Controller
{
    // ฟังก์ชันสำหรับเปิดหน้าห้องแชทของสินค้าชิ้นนั้น
    public function startChat(Product $product)
    {
        $user_id = Auth::id();

        if ($product->user_id === $user_id) {
            return back()->with('error', 'คุณไม่สามารถทักแชทหาสินค้าของตัวเองได้');
        }

        $conversation = Conversation::firstOrCreate([
            'product_id' => $product->id,
            'buyer_id'   => $user_id,
            'seller_id'  => $product->user_id,
        ]);


        return redirect()->route('chat.show', $conversation->id);
    }

    // ==========================================
    // หน้ารวมแชททั้งหมดของฉัน (Inbox)
    // ==========================================
    public function index()
    {
        $user_id = Auth::id();

        $conversations = Conversation::with(['product', 'buyer', 'seller'])
            ->where('buyer_id', $user_id)
            ->orWhere('seller_id', $user_id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    // หน้าแสดงข้อความในห้องแชท
    public function show(Conversation $conversation)
    {
        if (Auth::id() !== $conversation->buyer_id && Auth::id() !== $conversation->seller_id) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าดูแชทนี้');
        }

        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return view('chat.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        if (Auth::id() !== $conversation->buyer_id && Auth::id() !== $conversation->seller_id) {
            abort(403, 'คุณไม่มีสิทธิ์ส่งข้อความในแชทนี้');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'type' => 'text',
            'content' => $request->content,
        ]);
        $receiverId = ($conversation->buyer_id === Auth::id())
            ? $conversation->product->user_id
            : $conversation->buyer_id;

        $receiver = \App\Models\User::find($receiverId);

        // ส่ง Notification!
        if ($receiver) {
            $receiver->notify(new \App\Notifications\NewMessageNotification($conversation, $message));
        }

        return back();
    }

    // ==========================================
    // คนขายส่งบิล QR Code เข้าไปในแชท (ปรับราคาได้)
    // ==========================================
    public function sendBill(Request $request, \App\Models\Conversation $conversation)
    {
        $product = $conversation->product;
        $seller = Auth::user();

        if ($product->user_id !== $seller->id) abort(403);
        if ($product->status !== 'active') return back()->withErrors(['error' => 'สินค้านี้ติดจองหรือขายไปแล้ว']);
        if (empty($seller->phone)) return back()->withErrors(['error' => '❌ กรุณาตั้งค่าเบอร์ PromptPay ในโปรไฟล์ก่อนส่งบิลครับ']);

        $request->validate([
            'price' => 'required|numeric|min:1'
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($product, $conversation, $seller, $request) {

            $product->update(['status' => 'reserved']);

            \App\Models\Transaction::create([
                'user_id' => $conversation->buyer_id,
                'product_id' => $product->id,
                'amount' => $request->price,
                'payment_status' => 'pending',
            ]);

            $conversation->messages()->create([
                'sender_id' => $seller->id,
                'content' => '[BILL:' . $request->price . ':' . $seller->phone . ']'
            ]);
        });

        return back()->with('success', 'ส่งบิลเรียกเก็บเงินลงในแชทเรียบร้อย!');
    }
}
