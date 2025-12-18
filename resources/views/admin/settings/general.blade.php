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
