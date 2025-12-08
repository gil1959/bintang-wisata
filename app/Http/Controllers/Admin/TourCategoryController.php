<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourCategory;
use Illuminate\Http\Request;

class TourCategoryController extends Controller
{
    public function index()
    {
        $categories = TourCategory::orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:tour_categories,slug'
        ]);

        TourCategory::create($request->only('name', 'slug'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(TourCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, TourCategory $category)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:tour_categories,slug,' . $category->id
        ]);

        $category->update($request->only('name', 'slug'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(TourCategory $category)
    {
        // opsional: cek apakah masih dipakai tour package
        if ($category->packages()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan paket wisata.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
