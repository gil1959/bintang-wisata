@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">General Settings</h1>
      <p class="text-sm text-slate-600 mt-1">Atur konten global website dan konten Home Page dari sini.</p>
    </div>
  </div>

  {{-- Success --}}
  @if(session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
      <div class="flex items-start gap-3">
        <div class="mt-0.5">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 border border-emerald-200">
            ✓
          </span>
        </div>
        <div class="text-sm">
          <div class="font-semibold">Berhasil</div>
          <div class="text-emerald-800">{{ session('success') }}</div>
        </div>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.settings.general.save') }}" enctype="multipart/form-data"
        class="rounded-3xl border border-slate-200 bg-white shadow-sm">
    @csrf

    {{-- ================= HERO ================= --}}
    <div class="p-6 lg:p-8">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-extrabold text-slate-900">Hero (Home)</h2>
          <p class="text-sm text-slate-600 mt-1">Judul, subjudul, dan gambar hero section.</p>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Hero Title</label>
          <input
            name="hero_title"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('hero_title', $settings['hero_title'] ?? '') }}"
            placeholder="Perjalanan Nyaman & Terpercaya"
          />
          @error('hero_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Hero Subtitle</label>
          <textarea
            name="hero_subtitle"
            rows="3"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            placeholder="Kami membantu Anda merencanakan perjalanan..."
          >{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
          @error('hero_subtitle') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Hero Image</label>

          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
            <input type="file" name="hero_image" class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-600 file:px-4 file:py-2 file:text-white file:font-semibold hover:file:bg-sky-700" />
            <p class="text-xs text-slate-500 mt-2">Rekomendasi: JPG/PNG, landscape, ukuran optimal 1600px.</p>

            @if(!empty($settings['hero_image']))
              <div class="mt-4">
                <div class="text-xs font-semibold text-slate-700 mb-2">Preview</div>
                <img src="{{ $settings['hero_image'] }}" class="h-40 w-full rounded-2xl object-cover border border-slate-200" alt="Hero" />
              </div>
            @endif
          </div>

          @error('hero_image') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="h-px w-full bg-slate-200"></div>

    {{-- ================= FOOTER CONTACT ================= --}}
    <div class="p-6 lg:p-8">
      <h2 class="text-lg font-semibold mb-4">Branding</h2>
<p class="text-sm text-slate-600 mb-4">
  Pengaturan logo yang tampil di Navbar dan Footer.
</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
  <div class="bg-white border rounded-2xl p-4">
    <label class="block text-sm font-medium mb-1">Logo (Navbar & Footer)</label>
    <input type="file"
           name="site_logo"
           class="w-full border rounded-lg px-3 py-2 bg-white" />
    <p class="mt-1 text-xs text-slate-500">Rekomendasi: PNG transparan, tinggi 80–120px, max 2MB.</p>

    @if(empty($settings['site_logo']))
      <p class="mt-2 text-xs text-slate-500">
        Default: <code class="px-1 py-0.5 bg-slate-100 rounded">public/images/logo.png</code>
      </p>
    @endif
  </div>

  <div class="bg-white border rounded-2xl p-4">
    <label class="block text-sm font-medium mb-2">Preview Logo</label>

    @php
      $logo = !empty($settings['site_logo'])
              ? $settings['site_logo']
              : asset('images/logo.png');
    @endphp

    <div class="border rounded-xl bg-slate-50 p-4 flex items-center justify-center">
      <img src="{{ $logo }}" class="h-16 w-auto object-contain" alt="Logo Preview">
    </div>

    @if(!empty($settings['site_logo']))
      <p class="mt-2 text-xs text-slate-500 break-all">
        Sumber: <code class="px-1 py-0.5 bg-slate-100 rounded">{{ $settings['site_logo'] }}</code>
      </p>
    @endif
  </div>
</div>
      <h2 class="text-lg font-extrabold text-slate-900">Footer — Kontak</h2>
      <p class="text-sm text-slate-600 mt-1">Alamat, telepon, email, dan WhatsApp.</p>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Alamat</label>
          <textarea name="footer_address" rows="3"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            placeholder="Alamat lengkap..."
          >{{ old('footer_address', $settings['footer_address'] ?? '') }}</textarea>
          @error('footer_address') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Telepon</label>
          <input type="text" name="footer_phone"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('footer_phone', $settings['footer_phone'] ?? '') }}"
            placeholder="08xxxxxxxxxx"
          />
          @error('footer_phone') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Email</label>
          <input type="email" name="footer_email"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('footer_email', $settings['footer_email'] ?? '') }}"
            placeholder="info@domain.com"
          />
          @error('footer_email') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">WhatsApp (format: 628xxxx)</label>
          <input type="text" name="footer_whatsapp"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('footer_whatsapp', $settings['footer_whatsapp'] ?? '') }}"
            placeholder="6281234567890"
          />
          <p class="text-xs text-slate-500 mt-2">Tanpa tanda +, tanpa spasi.</p>
          @error('footer_whatsapp') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="h-px w-full bg-slate-200"></div>

    {{-- ================= EMAIL NOTIF ================= --}}
    <div class="p-6 lg:p-8">
      <h2 class="text-lg font-extrabold text-slate-900">Email Notifikasi</h2>
      <p class="text-sm text-slate-600 mt-1">Email admin untuk penerimaan invoice.</p>

      <div class="mt-6">
        <label class="block text-sm font-semibold text-slate-900 mb-2">Email Admin (kirim invoice)</label>
        <input type="email" name="invoice_admin_email"
          class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
          value="{{ old('invoice_admin_email', $settings['invoice_admin_email'] ?? '') }}"
          placeholder="admin@domain.com"
        />
        <p class="text-xs text-slate-500 mt-2">Kalau kosong, sistem akan pakai email di Footer.</p>
        @error('invoice_admin_email') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="h-px w-full bg-slate-200"></div>

    {{-- ================= ABOUT PAGE ================= --}}
    <div class="p-6 lg:p-8">
      <h2 class="text-lg font-extrabold text-slate-900">About Page</h2>
      <p class="text-sm text-slate-600 mt-1">Konten meta, hero, nilai kami, dan alur layanan.</p>

      <div class="mt-6 grid grid-cols-1 gap-5">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Meta Title</label>
          <input name="about_meta_title"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('about_meta_title', $settings['about_meta_title'] ?? '') }}"
            placeholder="Tentang Bintang Wisata"
          />
          @error('about_meta_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- HERO ABOUT --}}
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h3 class="text-base font-extrabold text-slate-900">Hero About</h3>
              <p class="text-xs text-slate-600 mt-1">Badge, judul, dan deskripsi.</p>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2">Hero Badge</label>
              <input name="about_hero_badge"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                value="{{ old('about_hero_badge', $settings['about_hero_badge'] ?? '') }}"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2">Hero Title</label>
              <input name="about_hero_title"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                value="{{ old('about_hero_title', $settings['about_hero_title'] ?? '') }}"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-slate-900 mb-2">Hero Description</label>
              <textarea name="about_hero_desc" rows="4"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              >{{ old('about_hero_desc', $settings['about_hero_desc'] ?? '') }}</textarea>
            </div>
          </div>
        </div>

        {{-- NILAI KAMI --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5">
          <h3 class="text-base font-extrabold text-slate-900">Section: Nilai Kami</h3>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2">Label</label>
              <input name="about_values_label"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                value="{{ old('about_values_label', $settings['about_values_label'] ?? '') }}" />
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2">Title</label>
              <input name="about_values_title"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                value="{{ old('about_values_title', $settings['about_values_title'] ?? '') }}" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-slate-900 mb-2">Description</label>
              <textarea name="about_values_desc" rows="3"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              >{{ old('about_values_desc', $settings['about_values_desc'] ?? '') }}</textarea>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            @for($i=1;$i<=4;$i++)
              <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <div class="text-sm font-extrabold text-slate-900 mb-3">Value Card {{ $i }}</div>

                <label class="block text-xs font-semibold text-slate-700 mb-2">Title</label>
                <input name="about_value{{ $i }}_title"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                  value="{{ old('about_value'.$i.'_title', $settings['about_value'.$i.'_title'] ?? '') }}" />

                <label class="block text-xs font-semibold text-slate-700 mt-4 mb-2">Description</label>
                <textarea name="about_value{{ $i }}_desc" rows="2"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                >{{ old('about_value'.$i.'_desc', $settings['about_value'.$i.'_desc'] ?? '') }}</textarea>
              </div>
            @endfor
          </div>
        </div>

        {{-- ALUR LAYANAN --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-5">
          <h3 class="text-base font-extrabold text-slate-900">Section: Alur Layanan</h3>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2">Label</label>
              <input name="about_flow_label"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                value="{{ old('about_flow_label', $settings['about_flow_label'] ?? '') }}" />
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2">Title</label>
              <input name="about_flow_title"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                value="{{ old('about_flow_title', $settings['about_flow_title'] ?? '') }}" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-slate-900 mb-2">Description</label>
              <textarea name="about_flow_desc" rows="3"
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              >{{ old('about_flow_desc', $settings['about_flow_desc'] ?? '') }}</textarea>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            @for($i=1;$i<=4;$i++)
              <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <div class="text-sm font-extrabold text-slate-900 mb-3">Step {{ $i }}</div>

                <label class="block text-xs font-semibold text-slate-700 mb-2">Title</label>
                <input name="about_step{{ $i }}_title"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                  value="{{ old('about_step'.$i.'_title', $settings['about_step'.$i.'_title'] ?? '') }}" />

                <label class="block text-xs font-semibold text-slate-700 mt-4 mb-2">Description</label>
                <textarea name="about_step{{ $i }}_desc" rows="2"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                >{{ old('about_step'.$i.'_desc', $settings['about_step'.$i.'_desc'] ?? '') }}</textarea>
              </div>
            @endfor
          </div>
        </div>
      </div>
    </div>

    <div class="h-px w-full bg-slate-200"></div>
<hr class="my-10">

<div class="flex items-start justify-between gap-4">
  <div>
    <h2 class="text-lg font-extrabold text-slate-900">Footer - Konten</h2>
    <p class="mt-1 text-sm text-slate-600">
      Atur teks kiri footer, judul "Tautan Cepat", daftar link, dan copyright.
    </p>
  </div>

  <span class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-100">
    <i data-lucide="layout-template" class="h-4 w-4"></i>
    Frontend
  </span>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Card: Tagline --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-2">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Tagline</div>
        <div class="mt-1 text-xs text-slate-500">
          Teks di sisi kiri footer (maks ~2-3 baris biar rapi).
        </div>
      </div>
      <i data-lucide="message-square-text" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4">
      <textarea
        name="footer_tagline"
        rows="4"
        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
        placeholder="Contoh: Partner perjalanan terpercaya untuk menjelajahi keindahan Indonesia..."
      >{{ old('footer_tagline', $settings['footer_tagline'] ?? '') }}</textarea>

      @error('footer_tagline')
        <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- Card: Quick Links Title + Copyright --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Judul & Copyright</div>
        <div class="mt-1 text-xs text-slate-500">Judul section link + teks copyright bawah.</div>
      </div>
      <i data-lucide="type" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 space-y-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Judul “Tautan Cepat”</label>
        <input
          type="text"
          name="footer_quick_links_title"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('footer_quick_links_title', $settings['footer_quick_links_title'] ?? '') }}"
          placeholder="Tautan Cepat"
        />
        @error('footer_quick_links_title')
          <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Copyright</label>
        <input
          type="text"
          name="footer_copyright"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}"
          placeholder="© 2025 Bintang Wisata Indonesia. All rights reserved."
        />
        @error('footer_copyright')
          <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </div>

  {{-- Card: Quick Links (repeatable 4) --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-3">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Daftar Link (Tautan Cepat)</div>
        <div class="mt-1 text-xs text-slate-500">
          Isi label dan URL. URL boleh <span class="font-semibold">/path</span> atau <span class="font-semibold">https://</span>.
        </div>
      </div>
      <i data-lucide="link-2" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
      @for($i=1; $i<=4; $i++)
        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
          <div class="flex items-center justify-between">
            <div class="text-xs font-extrabold text-slate-700">Link {{ $i }}</div>
            <span class="text-[11px] font-bold text-slate-500">Footer</span>
          </div>

          <div class="mt-3 grid grid-cols-1 lg:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Label</label>
              <input
                type="text"
                name="footer_link{{ $i }}_label"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
                value="{{ old('footer_link'.$i.'_label', $settings['footer_link'.$i.'_label'] ?? '') }}"
                placeholder="Contoh: Paket Tour"
              />
              @error('footer_link'.$i.'_label')
                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
              @enderror
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1.5">URL</label>
              <input
                type="text"
                name="footer_link{{ $i }}_url"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
                value="{{ old('footer_link'.$i.'_url', $settings['footer_link'.$i.'_url'] ?? '') }}"
                placeholder="/paket-tour atau https://instagram.com/..."
              />
              @error('footer_link'.$i.'_url')
                <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mt-3 text-[11px] text-slate-500">
            Tips: kalau mau nonaktifkan link, kosongin label atau URL.
          </div>
        </div>
      @endfor
    </div>
  </div>
</div>

    {{-- ================= HOME: HIGHLIGHTS ================= --}}
    <div class="p-6 lg:p-8">
      <h2 class="text-lg font-extrabold text-slate-900">Home Page — Highlights</h2>
      <p class="text-sm text-slate-600 mt-1">Section “Kenapa layanan kami beda” + 4 kartu kiri + 4 kartu kanan + CTA.</p>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Label</label>
          <input name="home_highlight_label"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_highlight_label', $settings['home_highlight_label'] ?? '') }}"
            placeholder="Kenapa layanan kami beda" />
          @error('home_highlight_label') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Title</label>
          <input name="home_highlight_title"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_highlight_title', $settings['home_highlight_title'] ?? '') }}"
            placeholder="Detail, rapi, dan fokus..." />
          @error('home_highlight_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Description</label>
          <textarea name="home_highlight_desc" rows="3"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            placeholder="Kami bikin trip terasa..."
          >{{ old('home_highlight_desc', $settings['home_highlight_desc'] ?? '') }}</textarea>
          @error('home_highlight_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
          <div class="text-sm font-extrabold text-slate-900 mb-4">Kartu Kiri (4)</div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @for ($i = 1; $i <= 4; $i++)
              <div class="rounded-3xl border border-slate-200 bg-white p-5">
                <div class="text-xs font-semibold text-slate-600 mb-3">Left {{ $i }}</div>

                <label class="block text-xs font-semibold text-slate-700 mb-2">Title</label>
                <input name="home_highlight_left{{ $i }}_title"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                  value="{{ old('home_highlight_left'.$i.'_title', $settings['home_highlight_left'.$i.'_title'] ?? '') }}" />

                <label class="block text-xs font-semibold text-slate-700 mt-4 mb-2">Description</label>
                <input name="home_highlight_left{{ $i }}_desc"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                  value="{{ old('home_highlight_left'.$i.'_desc', $settings['home_highlight_left'.$i.'_desc'] ?? '') }}" />
              </div>
            @endfor
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
          <div class="text-sm font-extrabold text-slate-900 mb-4">Kartu Kanan (4)</div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @for ($i = 1; $i <= 4; $i++)
              <div class="rounded-3xl border border-slate-200 bg-white p-5">
                <div class="text-xs font-semibold text-slate-600 mb-3">Right {{ $i }}</div>

                <label class="block text-xs font-semibold text-slate-700 mb-2">Title</label>
                <input name="home_highlight_right{{ $i }}_title"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                  value="{{ old('home_highlight_right'.$i.'_title', $settings['home_highlight_right'.$i.'_title'] ?? '') }}" />

                <label class="block text-xs font-semibold text-slate-700 mt-4 mb-2">Description</label>
                <input name="home_highlight_right{{ $i }}_desc"
                  class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
                  value="{{ old('home_highlight_right'.$i.'_desc', $settings['home_highlight_right'.$i.'_desc'] ?? '') }}" />
              </div>
            @endfor
          </div>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">CTA Primary Text</label>
          <input name="home_highlight_cta_primary_text"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_highlight_cta_primary_text', $settings['home_highlight_cta_primary_text'] ?? '') }}"
            placeholder="Mulai Jelajah Paket" />
          @error('home_highlight_cta_primary_text') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">CTA Secondary Text</label>
          <input name="home_highlight_cta_secondary_text"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_highlight_cta_secondary_text', $settings['home_highlight_cta_secondary_text'] ?? '') }}"
            placeholder="Cek Armada Rental" />
          @error('home_highlight_cta_secondary_text') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="h-px w-full bg-slate-200"></div>

    {{-- ================= HOME: WHY CHOOSE ================= --}}
    <div class="p-6 lg:p-8">
      <h2 class="text-lg font-extrabold text-slate-900">Home Page — Why Choose</h2>
      <p class="text-sm text-slate-600 mt-1">Section “Mengapa Memilih ...” + 4 kartu.</p>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Label</label>
          <input name="home_why_label"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_why_label', $settings['home_why_label'] ?? '') }}"
            placeholder="Layanan unggulan" />
          @error('home_why_label') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Title</label>
          <input name="home_why_title"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_why_title', $settings['home_why_title'] ?? '') }}"
            placeholder="Mengapa Memilih Bintang Wisata" />
          @error('home_why_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Description</label>
          <textarea name="home_why_desc" rows="3"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
          >{{ old('home_why_desc', $settings['home_why_desc'] ?? '') }}</textarea>
          @error('home_why_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        @for ($i = 1; $i <= 4; $i++)
          <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <div class="text-sm font-extrabold text-slate-900 mb-3">Card {{ $i }}</div>

            <label class="block text-xs font-semibold text-slate-700 mb-2">Title</label>
            <input name="home_why{{ $i }}_title"
              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              value="{{ old('home_why'.$i.'_title', $settings['home_why'.$i.'_title'] ?? '') }}" />

            <label class="block text-xs font-semibold text-slate-700 mt-4 mb-2">Description</label>
            <input name="home_why{{ $i }}_desc"
              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              value="{{ old('home_why'.$i.'_desc', $settings['home_why'.$i.'_desc'] ?? '') }}" />
          </div>
        @endfor
      </div>
    </div>

    <div class="h-px w-full bg-slate-200"></div>

    {{-- ================= HOME: BOOKING FLOW ================= --}}
    <div class="p-6 lg:p-8">
      <h2 class="text-lg font-extrabold text-slate-900">Home Page — Booking Flow</h2>
      <p class="text-sm text-slate-600 mt-1">Section “Cara Booking ...” + 4 step.</p>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Label</label>
          <input name="home_flow_label"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_flow_label', $settings['home_flow_label'] ?? '') }}"
            placeholder="Alur mudah" />
          @error('home_flow_label') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Title</label>
          <input name="home_flow_title"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
            value="{{ old('home_flow_title', $settings['home_flow_title'] ?? '') }}"
            placeholder="Cara Booking yang Rapi & Cepat" />
          @error('home_flow_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-semibold text-slate-900 mb-2">Description</label>
          <textarea name="home_flow_desc" rows="3"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
          >{{ old('home_flow_desc', $settings['home_flow_desc'] ?? '') }}</textarea>
          @error('home_flow_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        @for ($i = 1; $i <= 4; $i++)
          <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <div class="text-sm font-extrabold text-slate-900 mb-3">Step {{ $i }}</div>

            <label class="block text-xs font-semibold text-slate-700 mb-2">Title</label>
            <input name="home_flow{{ $i }}_title"
              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              value="{{ old('home_flow'.$i.'_title', $settings['home_flow'.$i.'_title'] ?? '') }}" />

            <label class="block text-xs font-semibold text-slate-700 mt-4 mb-2">Description</label>
            <input name="home_flow{{ $i }}_desc"
              class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-200"
              value="{{ old('home_flow'.$i.'_desc', $settings['home_flow'.$i.'_desc'] ?? '') }}" />
          </div>
        @endfor
      </div>
    </div>
<hr class="my-12">

{{-- =========================
  HALAMAN PAKET TOUR
========================= --}}
<div class="flex items-start justify-between gap-4">
  <div>
    <h2 class="text-lg font-extrabold text-slate-900">Halaman Paket Tour</h2>
    <p class="mt-1 text-sm text-slate-600">Atur teks hero, label filter, kotak tips, dan CTA.</p>
  </div>
  <span class="inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-100">
    <i data-lucide="map" class="h-4 w-4"></i>
    Tours
  </span>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Card: Hero --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-2">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Hero</div>
        <div class="mt-1 text-xs text-slate-500">Badge, judul, dan deskripsi bagian atas.</div>
      </div>
      <i data-lucide="sparkles" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Badge</label>
        <input
          name="tour_hero_badge"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_hero_badge', $settings['tour_hero_badge'] ?? '') }}"
          placeholder="Paket Tour"
        />
        @error('tour_hero_badge') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Title</label>
        <input
          name="tour_hero_title"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_hero_title', $settings['tour_hero_title'] ?? '') }}"
          placeholder="Temukan Paket Tour yang Sesuai..."
        />
        @error('tour_hero_title') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Description</label>
        <textarea
          name="tour_hero_desc"
          rows="4"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-400 focus:ring-sky-200"
          placeholder="Gunakan pencarian dan filter..."
        >{{ old('tour_hero_desc', $settings['tour_hero_desc'] ?? '') }}</textarea>
        @error('tour_hero_desc') <div class="mt-2 text-sm text-red-600">{{ $message }}</div> @enderror
      </div>
    </div>
  </div>

  {{-- Card: Filter Labels --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Label Filter</div>
        <div class="mt-1 text-xs text-slate-500">Teks pill filter di hero.</div>
      </div>
      <i data-lucide="sliders-horizontal" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 space-y-4">
      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Destinasi</label>
        <input name="tour_filter_dest_label"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_filter_dest_label', $settings['tour_filter_dest_label'] ?? '') }}"
          placeholder="Destinasi" />
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Kategori</label>
        <input name="tour_filter_cat_label"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_filter_cat_label', $settings['tour_filter_cat_label'] ?? '') }}"
          placeholder="Kategori" />
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Durasi</label>
        <input name="tour_filter_dur_label"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_filter_dur_label', $settings['tour_filter_dur_label'] ?? '') }}"
          placeholder="Durasi" />
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Transparan</label>
        <input name="tour_filter_trans_label"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_filter_trans_label', $settings['tour_filter_trans_label'] ?? '') }}"
          placeholder="Transparan" />
      </div>
    </div>
  </div>

  {{-- Card: Tips Box + Tips Items --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-2">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Kotak Tips</div>
        <div class="mt-1 text-xs text-slate-500">Judul, deskripsi, dan 4 item tips.</div>
      </div>
      <i data-lucide="lightbulb" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Judul</label>
        <input name="tour_tips_title"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_tips_title', $settings['tour_tips_title'] ?? '') }}"
          placeholder="Tips Cepat" />
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-slate-700 mb-2">Deskripsi</label>
        <input name="tour_tips_desc"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_tips_desc', $settings['tour_tips_desc'] ?? '') }}"
          placeholder="Gunakan kata kunci destinasi..." />
      </div>

      @for($i=1;$i<=4;$i++)
        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
          <div class="flex items-center justify-between">
            <div class="text-xs font-extrabold text-slate-700">Tip {{ $i }}</div>
            <span class="text-[11px] font-bold text-slate-500">Hero</span>
          </div>

          <div class="mt-3 space-y-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Title</label>
              <input name="tour_tip{{ $i }}_title"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
                value="{{ old('tour_tip'.$i.'_title', $settings['tour_tip'.$i.'_title'] ?? '') }}"
                placeholder="Contoh: Rekomendasi" />
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Description</label>
              <input name="tour_tip{{ $i }}_desc"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
                value="{{ old('tour_tip'.$i.'_desc', $settings['tour_tip'.$i.'_desc'] ?? '') }}"
                placeholder="Contoh: Paket favorit pelanggan" />
            </div>
          </div>
        </div>
      @endfor
    </div>
  </div>

  {{-- Card: CTA --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">CTA</div>
        <div class="mt-1 text-xs text-slate-500">Konten ajakan tindakan.</div>
      </div>
      <i data-lucide="megaphone" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 space-y-4">
      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CTA Title</label>
        <input name="tour_cta_title"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_cta_title', $settings['tour_cta_title'] ?? '') }}"
          placeholder="Membutuhkan Rekomendasi Paket?" />
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CTA Description</label>
        <textarea name="tour_cta_desc" rows="4"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          placeholder="Hubungi tim kami untuk konsultasi..."
        >{{ old('tour_cta_desc', $settings['tour_cta_desc'] ?? '') }}</textarea>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CTA Button</label>
        <input name="tour_cta_button"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
          value="{{ old('tour_cta_button', $settings['tour_cta_button'] ?? '') }}"
          placeholder="Konsultasi via WhatsApp" />
      </div>
      <div>
  <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CTA Secondary Button (Lihat Rental)</label>
  <input name="tour_cta_secondary_button"
    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
    value="{{ old('tour_cta_secondary_button', $settings['tour_cta_secondary_button'] ?? '') }}"
    placeholder="Lihat Rental" />
</div>

<div>
  <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CTA Secondary Link</label>
  <input name="tour_cta_secondary_link"
    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:ring-sky-200"
    value="{{ old('tour_cta_secondary_link', $settings['tour_cta_secondary_link'] ?? '') }}"
    placeholder="/rent-car atau https://..." />
  <p class="mt-2 text-[11px] text-slate-500">Boleh pakai route path seperti <span class="font-semibold">/rent-car</span> atau URL penuh.</p>
</div>

    </div>
  </div>
</div>


<hr class="my-12">

{{-- =========================
  HALAMAN RENT CAR
========================= --}}
<div class="flex items-start justify-between gap-4">
  <div>
    <h2 class="text-lg font-extrabold text-slate-900">Halaman Rent Car</h2>
    <p class="mt-1 text-sm text-slate-600">Atur teks hero, chip, catatan, dan 4 kartu benefit.</p>
  </div>
  <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
    <i data-lucide="car" class="h-4 w-4"></i>
    Rentcar
  </span>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Hero --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-2">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Hero</div>
        <div class="mt-1 text-xs text-slate-500">Badge, judul, dan deskripsi.</div>
      </div>
      <i data-lucide="sparkles" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Badge</label>
        <input name="rentcar_hero_badge"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
          value="{{ old('rentcar_hero_badge', $settings['rentcar_hero_badge'] ?? '') }}"
          placeholder="Rental Mobil" />
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Title</label>
        <input name="rentcar_hero_title"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
          value="{{ old('rentcar_hero_title', $settings['rentcar_hero_title'] ?? '') }}"
          placeholder="Pilihan Mobil Terbaik..." />
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Description</label>
        <textarea name="rentcar_hero_desc" rows="4"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
          placeholder="Armada terawat, harga transparan..."
        >{{ old('rentcar_hero_desc', $settings['rentcar_hero_desc'] ?? '') }}</textarea>
      </div>
    </div>
  </div>

  {{-- Chips --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Chips</div>
        <div class="mt-1 text-xs text-slate-500">Teks pill kecil di hero.</div>
      </div>
      <i data-lucide="tags" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 space-y-3">
      @for($i=1;$i<=4;$i++)
        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Chip {{ $i }}</label>
          <input name="rentcar_chip{{ $i }}"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
            value="{{ old('rentcar_chip'.$i, $settings['rentcar_chip'.$i] ?? '') }}"
            placeholder="Contoh: Terawat" />
        </div>
      @endfor
    </div>
  </div>

  {{-- Note + Cards --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-3">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Kotak Catatan & Kartu Benefit</div>
        <div class="mt-1 text-xs text-slate-500">Judul/desc catatan dan 4 kartu.</div>
      </div>
      <i data-lucide="sticky-note" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 lg:col-span-1">
        <div class="text-xs font-extrabold text-slate-700">Catatan</div>

        <div class="mt-3">
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Judul</label>
          <input name="rentcar_note_title"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
            value="{{ old('rentcar_note_title', $settings['rentcar_note_title'] ?? '') }}"
            placeholder="Catatan" />
        </div>

        <div class="mt-3">
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Deskripsi</label>
          <input name="rentcar_note_desc"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
            value="{{ old('rentcar_note_desc', $settings['rentcar_note_desc'] ?? '') }}"
            placeholder="Klik “Booking Sekarang”..." />
        </div>
      </div>

      <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
        @for($i=1;$i<=4;$i++)
          <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <div class="flex items-center justify-between">
              <div class="text-xs font-extrabold text-slate-700">Card {{ $i }}</div>
              <span class="text-[11px] font-bold text-slate-500">Rentcar</span>
            </div>

            <div class="mt-3 space-y-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Title</label>
                <input name="rentcar_note{{ $i }}_title"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
                  value="{{ old('rentcar_note'.$i.'_title', $settings['rentcar_note'.$i.'_title'] ?? '') }}"
                  placeholder="Contoh: Hemat" />
              </div>

              <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Description</label>
                <input name="rentcar_note{{ $i }}_desc"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-emerald-400 focus:ring-emerald-200"
                  value="{{ old('rentcar_note'.$i.'_desc', $settings['rentcar_note'.$i.'_desc'] ?? '') }}"
                  placeholder="Contoh: Nyaman untuk perjalanan" />
              </div>
            </div>
          </div>
        @endfor
      </div>
    </div>
  </div>
</div>


<hr class="my-12">

{{-- =========================
  HALAMAN DOKUMENTASI
========================= --}}
<div class="flex items-start justify-between gap-4">
  <div>
    <h2 class="text-lg font-extrabold text-slate-900">Halaman Dokumentasi</h2>
    <p class="mt-1 text-sm text-slate-600">Atur teks hero, label tab, label statistik, dan hint.</p>
  </div>
  <span class="inline-flex items-center gap-2 rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700 ring-1 ring-violet-100">
    <i data-lucide="image" class="h-4 w-4"></i>
    Docs
  </span>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Hero --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 lg:col-span-2">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">Hero</div>
        <div class="mt-1 text-xs text-slate-500">Badge, judul, dan deskripsi.</div>
      </div>
      <i data-lucide="sparkles" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Badge</label>
        <input name="docs_hero_badge"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
          value="{{ old('docs_hero_badge', $settings['docs_hero_badge'] ?? '') }}"
          placeholder="Dokumentasi Perjalanan" />
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Title</label>
        <input name="docs_hero_title"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
          value="{{ old('docs_hero_title', $settings['docs_hero_title'] ?? '') }}"
          placeholder="Dokumentasi" />
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-slate-700 mb-2">Hero Description</label>
        <textarea name="docs_hero_desc" rows="4"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
          placeholder="Galeri dokumentasi perjalanan..."
        >{{ old('docs_hero_desc', $settings['docs_hero_desc'] ?? '') }}</textarea>
      </div>
    </div>
  </div>

  {{-- Tabs + Stats + Hint --}}
  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="text-sm font-extrabold text-slate-900">UI Label</div>
        <div class="mt-1 text-xs text-slate-500">Tab & label statistik.</div>
      </div>
      <i data-lucide="layers" class="h-5 w-5 text-slate-400"></i>
    </div>

    <div class="mt-4 space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Tab Foto</label>
          <input name="docs_tab_photos"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
            value="{{ old('docs_tab_photos', $settings['docs_tab_photos'] ?? '') }}"
            placeholder="Foto" />
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Tab Video</label>
          <input name="docs_tab_videos"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
            value="{{ old('docs_tab_videos', $settings['docs_tab_videos'] ?? '') }}"
            placeholder="Video" />
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Label Statistik Foto</label>
          <input name="docs_stat_photos"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
            value="{{ old('docs_stat_photos', $settings['docs_stat_photos'] ?? '') }}"
            placeholder="Total Foto" />
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Label Statistik Video</label>
          <input name="docs_stat_videos"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
            value="{{ old('docs_stat_videos', $settings['docs_stat_videos'] ?? '') }}"
            placeholder="Total Video" />
        </div>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Hint bawah tab</label>
        <input name="docs_hint"
          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-violet-400 focus:ring-violet-200"
          value="{{ old('docs_hint', $settings['docs_hint'] ?? '') }}"
          placeholder="Gunakan tab untuk menavigasi..." />
      </div>

      <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-xs text-slate-600">
        Tips: Keep label pendek biar UI tab & stat nggak kepanjangan.
      </div>
    </div>
  </div>
</div>

    {{-- Sticky Footer Actions --}}
    <div class="sticky bottom-0 z-10 border-t  px-6 lg:px-8 py-4 rounded-b-3xl">
      <div class="flex items-center justify-end gap-3">
        <button class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-white font-extrabold hover:bg-sky-700 transition"
                type="submit">
          Simpan Perubahan
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
