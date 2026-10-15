@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
  .ql-toolbar.ql-snow { border-top-left-radius: 12px; border-top-right-radius: 12px; }
  .ql-container.ql-snow { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
  .ql-editor { min-height: 320px; font-family: Nunito, ui-sans-serif, system-ui; font-size: 14px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Quill) return;

    // Untuk setiap textarea.wysiwyg:
    // - textarea disembunyikan
    // - dibuat div editor Quill setelahnya
    // - saat submit form, HTML editor ditaruh balik ke textarea
    document.querySelectorAll('textarea.wysiwyg').forEach(function (ta, idx) {
        // Hindari init dobel
        if (ta.dataset.wysiwygInit === '1') return;
        ta.dataset.wysiwygInit = '1';

        const wrapper = document.createElement('div');
        wrapper.className = 'mt-2';
        const editor = document.createElement('div');
        editor.id = 'quill-editor-' + idx;

        wrapper.appendChild(editor);

        // Sisipkan editor setelah textarea
        ta.insertAdjacentElement('afterend', wrapper);

        // Sembunyikan textarea, tapi tetap dipakai untuk submit
        ta.style.display = 'none';

        const q = new Quill('#' + editor.id, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Set initial content dari textarea (edit page)
        let initialHtml = ta.value || '';
        // Jika konten di database mengandung HTML entity double-escaped (e.g. &lt;p&gt;), decode sekali agar tidak tampil sebagai raw HTML text
        if (initialHtml.includes('&lt;') && initialHtml.includes('&gt;')) {
            const parser = new DOMParser();
            const decoded = parser.parseFromString(initialHtml, 'text/html').body.textContent;
            if (decoded && decoded.includes('<')) {
                initialHtml = decoded;
            }
        }
        q.root.innerHTML = initialHtml;

        // Sinkronisasi realtime ke textarea
        q.on('text-change', function () {
            const text = q.getText().trim();
            ta.value = text.length === 0 ? '' : q.root.innerHTML;
        });

        // Saat submit: pastikan textarea value sinkron dan bersihkan tag kosong
        const form = ta.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                const text = q.getText().trim();
                ta.value = text.length === 0 ? '' : q.root.innerHTML;
            });
        }
    });
});
</script>
@endpush
