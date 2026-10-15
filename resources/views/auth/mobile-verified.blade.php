<x-guest-layout>
    <x-auth.auth-wrapper>
        <div class="text-center">
            <div class="inline-flex items-center justify-center rounded-full mb-5" style="width:76px;height:76px;background:{{ $verified ? '#E7F6ED' : '#FFF4E2' }};color:{{ $verified ? '#187C44' : '#886000' }}">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    @if($verified)
                        <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                    @else
                        <circle cx="12" cy="12" r="9" /><path d="M12 7v6m0 4h.01" stroke-linecap="round" />
                    @endif
                </svg>
            </div>
            <p class="text-xs font-semibold mb-2" style="color:#0194F3">VERIFIKASI AKUN</p>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $verified ? 'Email berhasil diverifikasi' : 'Tautan verifikasi tidak berlaku' }}</h1>
            <p class="text-sm text-gray-600 mb-6" style="line-height:1.75">{{ $verified ? 'Akun Bintang Wisata kamu sudah terverifikasi. Kembali ke aplikasi untuk melanjutkan pemesanan dan mengelola perjalananmu.' : 'Tautan ini sudah kedaluwarsa atau tidak valid. Buka aplikasi dan kirim ulang email verifikasi dari halaman akun.' }}</p>
        </div>
        <a href="bintangwisata://auth/verified" class="flex items-center justify-center rounded-xl text-white px-5 py-3 font-semibold" style="background:#0194F3;text-align:center">{{ $verified ? 'Kembali ke aplikasi' : 'Buka aplikasi' }}</a>
        <a href="{{ url('/') }}" class="block text-center text-sm font-semibold mt-4" style="color:#0194F3">Buka situs Bintang Wisata</a>
        <p class="text-center text-xs text-gray-500 mt-6" style="line-height:1.7">Jika aplikasi belum terbuka, buka Bintang Wisata secara manual lalu periksa status akunmu.</p>
    </x-auth.auth-wrapper>
</x-guest-layout>
