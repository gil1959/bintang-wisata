<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button" @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Pengaturan SEO</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-6">
        <div class="flex flex-col md:flex-row gap-8">
            {{-- Kiri: Image Upload --}}
            <div class="w-full md:w-1/3 flex flex-col items-center md:border-r border-slate-100 pr-0 md:pr-4">
                <label class="block text-sm font-bold text-slate-800 mb-4 self-start">SEO Image</label>
                
                <div class="relative w-full aspect-video rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100 transition flex items-center justify-center overflow-hidden group cursor-pointer"
                     onclick="document.getElementById('seo_image_input').click()">
                    
                    @if(!empty($model?->seo_image_path))
                        <img id="seo_image_preview" src="{{ asset('storage/' . $model->seo_image_path) }}" class="w-full h-full object-cover">
                        <div id="seo_image_placeholder" class="hidden flex flex-col items-center justify-center text-slate-400">
                            <i data-lucide="image" class="w-10 h-10 mb-2"></i>
                            <span class="text-xs font-semibold">Klik untuk unggah</span>
                        </div>
                    @else
                        <img id="seo_image_preview" src="" class="w-full h-full object-cover hidden">
                        <div id="seo_image_placeholder" class="flex flex-col items-center justify-center text-slate-400">
                            <i data-lucide="image" class="w-10 h-10 mb-2"></i>
                            <span class="text-xs font-semibold">Klik untuk unggah</span>
                        </div>
                    @endif

                    <div class="absolute bottom-2 right-2 bg-[#0194F3] text-white p-2 rounded-full shadow-lg group-hover:scale-110 transition">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    </div>
                </div>

                <input type="file" name="seo_image" id="seo_image_input" accept="image/png, image/jpeg, image/jpg" class="hidden"
                    onchange="
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('seo_image_preview').src = e.target.result;
                                document.getElementById('seo_image_preview').classList.remove('hidden');
                                const placeholder = document.getElementById('seo_image_placeholder');
                                if(placeholder) placeholder.classList.add('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    ">

                <p class="mt-4 text-xs text-slate-500 text-center leading-relaxed">
                    Supported Files: <span class="font-semibold text-slate-700">.png, .jpg, .jpeg.</span><br>
                    Image will be resized into <span class="font-semibold text-slate-700">1180x600px</span>
                </p>
            </div>

            {{-- Kanan: Text Inputs --}}
            <div class="w-full md:w-2/3 flex flex-col space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1 flex items-center flex-wrap gap-1">
                        Meta Keywords 
                        <span class="text-[11px] font-normal text-slate-500">(Separate multiple keywords by , (comma) or enter key)</span>
                    </label>
                    <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $model->seo_keywords ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-[#0194F3]/20 focus:border-[#0194F3]"
                           placeholder="Enter keywords...">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Meta Description</label>
                    <textarea name="seo_description" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-[#0194F3]/20 focus:border-[#0194F3]"
                              placeholder="Enter meta description...">{{ old('seo_description', $model->seo_description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Social Title</label>
                    <input type="text" name="social_title" value="{{ old('social_title', $model->social_title ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-[#0194F3]/20 focus:border-[#0194F3]"
                           placeholder="Enter social title...">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Social Description</label>
                    <textarea name="social_description" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-[#0194F3]/20 focus:border-[#0194F3]"
                              placeholder="Enter social description...">{{ old('social_description', $model->social_description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
