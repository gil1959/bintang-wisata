<x-guest-layout>
    <x-auth.auth-wrapper>

        <h2 class="text-xl font-bold text-gray-900 mb-2">
            Verifikasi Email
        </h2>

        <p class="text-sm text-gray-600 mb-6">
            Kami telah mengirimkan link verifikasi ke email Anda.
            Silakan klik link tersebut untuk mengaktifkan akun.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm text-emerald-600 font-medium">
                Link verifikasi baru telah dikirim.
            </div>
        @endif

        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="px-4 py-2 rounded-xl bg-[#0194F3] text-white font-semibold hover:opacity-90">
                    Kirim Ulang Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-gray-500 hover:underline">
                    Logout
                </button>
            </form>
        </div>

    </x-auth.auth-wrapper>
</x-guest-layout>
