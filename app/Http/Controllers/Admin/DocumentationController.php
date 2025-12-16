<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type'); // photo|video|null
        $q = Documentation::query();

        if ($type && in_array($type, ['photo', 'video'])) {
            $q->where('type', $type);
        }

        $items = $q->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.documentations.index', compact('items', 'type'));
    }

    public function create()
    {
        return view('admin.documentations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:photo,video'],
            'title' => ['nullable', 'string', 'max:120'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                // Foto: jpg/png/webp. Video: mp4/webm/ogg (lu bisa tambah mov kalau perlu)
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg',
                'max:51200' // 50MB per file
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $type = $data['type'];
        $isActive = $request->boolean('is_active', true);

        $created = 0;

        foreach ($request->file('files') as $file) {
            $isImage = str_starts_with($file->getMimeType(), 'image/');
            $isVideo = str_starts_with($file->getMimeType(), 'video/');

            // Cocokkan file dengan type yang dipilih (biar nggak salah upload)
            if ($type === 'photo' && !$isImage) continue;
            if ($type === 'video' && !$isVideo) continue;

            $dir = $type === 'photo' ? 'documentations/photos' : 'documentations/videos';
            $path = $file->store($dir, 'public');

            Documentation::create([
                'type' => $type,
                'title' => $data['title'] ?? null,
                'file_path' => $path,
                'is_active' => $isActive,
                'sort_order' => 0,
            ]);

            $created++;
        }

        return redirect()->route('admin.documentations.index')
            ->with('success', "Berhasil menambahkan {$created} file dokumentasi.");
    }

    public function edit(Documentation $documentation)
    {
        return view('admin.documentations.edit', compact('documentation'));
    }

    public function update(Request $request, Documentation $documentation)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'replace_file' => [
                'nullable',
                'file',
                'max:51200',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg'
            ],
        ]);

        if ($request->hasFile('replace_file')) {
            // hapus lama
            Storage::disk('public')->delete($documentation->file_path);

            $file = $request->file('replace_file');
            $dir = $documentation->type === 'photo' ? 'documentations/photos' : 'documentations/videos';
            $path = $file->store($dir, 'public');

            $documentation->file_path = $path;
        }

        $documentation->title = $data['title'] ?? $documentation->title;
        $documentation->is_active = $request->boolean('is_active', $documentation->is_active);
        $documentation->sort_order = $data['sort_order'] ?? $documentation->sort_order;
        $documentation->save();

        return redirect()->route('admin.documentations.index')->with('success', 'Dokumentasi diperbarui.');
    }

    public function destroy(Documentation $documentation)
    {
        Storage::disk('public')->delete($documentation->file_path);
        $documentation->delete();

        return back()->with('success', 'Dokumentasi dihapus.');
    }
}
