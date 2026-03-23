<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    style="--tenant-primary: {{ tenant('primary_color') ?? '#3B82F6' }};">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ tenant("name") }} - HaRaiDee</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
    <nav class="bg-tenant-primary text-white p-4 shadow-md">

        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('products.index') }}" class="text-xl font-bold hover:opacity-80 transition">{{
                tenant("name") }}</a>


            <div style="position: relative; display: inline-block; margin-right: 20px;">
                <a href="#" style="text-decoration: none; font-size: 1.5em; color: gray;">
                    🔔
                    @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                    <span
                        style="position: absolute; top: -5px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.5em; font-weight: bold;">
                        {{ Auth::user()->unreadNotifications->count() }}
                    </span>
                    @endif
                </a>

                @if(Auth::check() && Auth::user()->notifications->count() > 0)
                <div
                    style="position: absolute; right: 0; top: 35px; width: 300px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 10px; z-index: 1000;">
                    <h4 style="margin: 0 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 5px;">แจ้งเตือนล่าสุด
                    </h4>

                    @foreach(Auth::user()->notifications->take(5) as $notification)
                    <a href="{{ route('chat.show', $notification->data['conversation_id']) }}"
                        style="display: block; padding: 10px; border-bottom: 1px solid #f9f9f9; text-decoration: none; color: #333; background: {{ $notification->read_at === null ? '#f0f8ff' : 'white' }};">
                        <b style="color: {{ tenant('primary_color') ?? '#007bff' }}">{{
                            $notification->data['sender_name'] }}</b>
                        ส่งข้อความถึงคุณใน <b>{{ Str::limit($notification->data['product_title'], 20) }}</b><br>
                        <span style="font-size: 0.8em; color: gray;">"{{
                            Str::limit($notification->data['message_content'], 30) }}"</span>
                    </a>

                    @php $notification->markAsRead(); @endphp
                    @endforeach
                </div>
                @endif
            </div>


            <div class="flex items-center space-x-4">
                @guest
                <a href="{{ route('login') }}"
                    class="hover:bg-black/20 px-3 py-2 rounded-md transition font-medium">เข้าสู่ระบบ</a>
                @endguest @auth
                <span class="text-sm bg-black/10 px-3 py-1 rounded-full">👤 {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="hover:bg-red-500/80 px-3 py-2 rounded-md transition text-sm font-medium">
                        ออกจากระบบ
                    </button>
                </form>
                @endauth
            </div>
        </div>

    </nav>
    <main class="container mx-auto mt-8 p-4">@yield('content')</main>
</body>

</html>