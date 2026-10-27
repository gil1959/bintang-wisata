<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
    <body class="font-sans antialiased">
        @if(session()->has('impersonating_admin_id') || session()->has('impersonator_id'))
            <div class="bg-amber-500 text-white text-xs sm:text-sm font-bold px-4 py-2 flex items-center justify-between sticky top-0 z-50 shadow-md">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Mode Penyamaran: Anda sedang login sebagai {{ auth()->user()->name ?? 'Pengguna' }}</span>
                </div>
                <a href="{{ route('leave-impersonate') }}" class="bg-white text-amber-700 hover:bg-amber-50 px-3 py-1 rounded-lg text-xs font-extrabold transition">
                    Kembali ke Admin
                </a>
            </div>
        @endif
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
         <script>
        (async function(){
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

            try {
                const reg = await navigator.serviceWorker.register('/sw.js');

                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return;

                const vapidPublicKey = @json(env('VAPID_PUBLIC_KEY'));
                if (!vapidPublicKey) return;

                function urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                    return outputArray;
                }

                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
                });

                await fetch(@json(route('push.subscribe')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify(sub)
                });
            } catch (e) {
                
            }
        })();
        </script>
    </body>
</html>
