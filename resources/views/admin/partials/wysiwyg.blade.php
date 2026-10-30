@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Poppins:wght@400;500;600;700&display=swap">
<style>
  .ql-toolbar.ql-snow {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    border-color: #cbd5e1;
    background-color: #f8fafc;
  }
  .ql-container.ql-snow {
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    border-color: #cbd5e1;
    background-color: #ffffff;
    font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, sans-serif;
  }
  .ql-editor {
    min-height: 340px;
    font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, sans-serif;
    font-size: 15px;
    line-height: 1.75;
    color: #334155;
  }
  .ql-editor h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
    color: #0f172a;
    line-height: 1.25;
  }
  .ql-editor h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #1e293b;
    line-height: 1.3;
  }
  .ql-editor h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    color: #334155;
    line-height: 1.4;
  }
  .ql-editor h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    color: #475569;
  }
  .ql-editor p {
    margin-bottom: 1.15rem;
  }
  .ql-editor a {
    color: #0194F3;
    text-decoration: underline;
    text-underline-offset: 3px;
    font-weight: 500;
  }
  .ql-editor blockquote {
    border-left: 4px solid #0194F3;
    padding-left: 1rem;
    color: #475569;
    font-style: italic;
    background: #f8fafc;
    border-radius: 0 8px 8px 0;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    margin: 1.25rem 0;
  }
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
                    [{ header: [1, 2, 3, 4, false] }],
                    [{ font: [] }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ align: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ indent: '-1' }, { indent: '+1' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
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
