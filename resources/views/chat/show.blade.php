<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ห้องแชท - {{ $conversation->product->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --tenant-primary: {
                    {
                    tenant('primary_color') ?? '#0ea5e9'
                }
            }

            ;
            /* Default: Tailwind Sky 500 */
        }

        /* จำลองคลาสสีเพื่อให้ใช้กับ Tailwind ได้ทันที (หรือไปตั้งใน tailwind.config.js) */
        .bg-tenant {
            background-color: var(--tenant-primary);
        }

        .text-tenant {
            color: var(--tenant-primary);
        }

        .border-tenant {
            border-color: var(--tenant-primary);
        }

        .ring-tenant {
            --tw-ring-color: var(--tenant-primary);
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans antialiased h-screen flex justify-center">

    <div class="w-full max-w-md bg-white h-full flex flex-col shadow-2xl relative">

        <header
            class="bg-white/90 backdrop-blur-md border-b border-gray-100 px-4 py-3 flex items-center space-x-3 z-10 sticky top-0 shadow-sm">
            <a href="{{ route('products.show', $conversation->product_id) }}"
                class="text-gray-500 hover:text-tenant transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold text-gray-900 truncate">
                    {{ $conversation->product->title }}
                </h2>
                <p class="text-xs text-gray-500 truncate">
                    {{ $conversation->buyer->name }} • {{ $conversation->seller->name }}
                </p>
            </div>
            <div
                class="h-10 w-10 rounded-full bg-tenant text-white flex items-center justify-center font-bold shadow-md">
                {{ mb_substr($conversation->product->status == 'active' ? '🟢' : '⏳', 0, 1) }}
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 space-y-6 bg-slate-50 flex flex-col" x-data
            x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })">
            @forelse($messages as $message)
            @php
            $isMe = $message->sender_id === Auth::id();
            @endphp

            <div class="flex w-full {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="flex flex-col max-w-[80%] {{ $isMe ? 'items-end' : 'items-start' }}">

                    <span class="text-[10px] text-gray-400 mb-1 px-1">
                        {{ $message->sender->name }}
                    </span>

                    @if(str_starts_with($message->content, '[BILL:'))
                    @php
                    $parts = explode(':', str_replace(']', '', $message->content));
                    $billAmount = $parts[1] ?? 0;
                    $billPhone = $parts[2] ?? '';
                    $qrUrl = "https://promptpay.io/{$billPhone}/{$billAmount}.png";
                    @endphp

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden w-64 mt-1">
                        <div class="bg-tenant px-4 py-3 text-center">
                            <h4 class="text-white font-semibold text-sm tracking-wide">🧾 บิลเรียกเก็บเงิน</h4>
                        </div>
                        <div class="p-5 text-center bg-gradient-to-b from-white to-slate-50">
                            <h2 class="text-3xl font-extrabold text-gray-800 mb-4">
                                <span class="text-lg text-gray-400">฿</span>{{ number_format((float)$billAmount, 2) }}
                            </h2>
                            <div class="p-2 bg-white rounded-xl shadow-inner border border-gray-100 inline-block">
                                <img src="{{ $qrUrl }}" alt="PromptPay QR" class="w-40 h-40 object-cover rounded-lg">
                            </div>
                            <div
                                class="mt-4 flex items-center justify-center space-x-1 text-xs text-gray-500 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-tenant" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                <span>{{ $billPhone }}</span>
                            </div>
                        </div>
                    </div>

                    @else
                    <div class="px-4 py-2.5 rounded-2xl text-sm md:text-base leading-relaxed break-words shadow-sm
            {{ $isMe 
                ? 'bg-tenant text-black rounded-tr-sm' 
                : 'bg-white text-gray-700 border border-gray-100 rounded-tl-sm' }}">
                        {{ $message->content }}
                    </div>
                    @endif

                </div>
            </div>
            @empty
            <div class="m-auto flex flex-col items-center justify-center opacity-50">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                    <span class="text-3xl">👋</span>
                </div>
                <p class="text-sm font-medium text-gray-500">ยังไม่มีข้อความ เริ่มทักทายเลย!</p>
            </div>
            @endforelse
        </main>

        <footer class="bg-white border-t border-gray-100 p-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">

            @if(Auth::id() == $conversation->product->user_id)
            <div class="mb-3 px-1">
                @if($conversation->product->status == 'active')
                <form action="{{ route('chat.bill.send', $conversation->id) }}" method="POST"
                    class="flex items-center gap-2 bg-slate-50 border border-slate-200 p-2 rounded-xl">
                    @csrf
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1 relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">฿</span>
                        <input type="number" name="price" value="{{ $conversation->product->price }}" min="1" required
                            class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-tenant transition-shadow">
                    </div>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1 shadow-sm">
                        <span>เรียกเก็บ</span>
                    </button>
                </form>
                @elseif($conversation->product->status == 'reserved')
                <div
                    class="flex items-center justify-center gap-2 bg-amber-50 text-amber-700 p-2.5 rounded-xl border border-amber-200 text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    รอการโอนเงิน (สินค้าถูกล็อก)
                </div>
                @endif
            </div>
            @endif

            @if(Auth::id() == $conversation->buyer_id && $conversation->product->status == 'sold')
            @php
            $transaction = \App\Models\Transaction::where('product_id', $conversation->product_id)
            ->where('user_id', Auth::id())
            ->where('payment_status', 'completed')
            ->first();
            @endphp

            @if($transaction)
            <div
                style="background: #e8f4f8; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #b8daff;">
                @if($transaction->review)
                <div style="text-align: center; color: #17a2b8;">
                    <b>⭐ คุณให้คะแนนร้านค้านี้ไปแล้ว {{ $transaction->review->rating }} ดาว</b><br>
                    <span style="font-size: 0.9em; color: gray;">"{{ $transaction->review->comment }}"</span>
                </div>
                @else
                <h4 style="margin-top: 0; color: #0056b3;">⭐ ให้คะแนนผู้ขาย ({{ $conversation->seller->name }})</h4>
                <form action="{{ route('reviews.store', $transaction->id) }}" method="POST"
                    style="display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div>
                        <label style="font-weight: bold;">คะแนน:</label>
                        <select name="rating" required
                            style="padding: 5px; border-radius: 4px; width: 100%; border: 1px solid #ccc;">
                            <option value="5">⭐⭐⭐⭐⭐ (5) ดีเยี่ยม</option>
                            <option value="4">⭐⭐⭐⭐ (4) ดี</option>
                            <option value="3">⭐⭐⭐ (3) ปานกลาง</option>
                            <option value="2">⭐⭐ (2) พอใช้</option>
                            <option value="1">⭐ (1) แย่</option>
                        </select>
                    </div>
                    <input type="text" name="comment" placeholder="เขียนรีวิวสั้นๆ (ไม่บังคับ)"
                        style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
                    <button type="submit"
                        style="background: #28a745; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        ส่งรีวิว
                    </button>
                </form>
                @endif
            </div>
            @endif
            @endif

            <form action="{{ route('chat.sendMessage', $conversation->id) }}" method="POST"
                class="flex items-center gap-2">
                @csrf
                <div class="flex-1 relative">
                    <input type="text" name="content" required autofocus autocomplete="off"
                        placeholder="พิมพ์ข้อความ..."
                        class="w-full bg-gray-100 text-gray-800 placeholder-gray-400 px-4 py-3 rounded-full text-sm border-none focus:ring-2 focus:ring-tenant focus:bg-white transition-all outline-none">
                </div>
                <button type="submit"
                    class="w-11 h-11 flex items-center justify-center bg-tenant text-black rounded-full shadow-md hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tenant shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 translate-x-px translate-y-px"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>

        </footer>
    </div>
</body>

</html>