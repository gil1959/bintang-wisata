<x-guest-layout>
    <x-auth.auth-wrapper>
        <h1 class="text-xl font-bold mb-4">{{ $error ? 'Login belum berhasil' : 'Login Google berhasil' }}</h1>
        <p class="text-slate-600 mb-6">{{ $error ?: 'Kembali ke aplikasi Bintang Wisata untuk melanjutkan.' }}</p>
        <a href="bintangwisata://auth/google?request_id={{ $requestId }}" class="block text-center rounded-xl bg-[#0194F3] text-white px-5 py-3 font-semibold">Kembali ke aplikasi</a>
    </x-auth.auth-wrapper>
</x-guest-layout>
