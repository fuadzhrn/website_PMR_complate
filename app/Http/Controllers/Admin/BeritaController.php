<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::latest()->paginate(10);
        return view('admin.berita.index', compact('berita'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'nullable|string|max:50',
            'location'    => 'nullable|string|max:150',
            'author'      => 'nullable|string|max:100',
            'paragraphs'  => 'nullable|string',
            'is_featured' => 'boolean',
            'image'       => 'nullable|image|max:4096',
        ]);

        $data['slug']        = Str::slug($data['title']) . '-' . Str::random(5);
        $data['paragraphs']  = $this->parseParagraphs($request->input('paragraphs'));
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'news');
        }

        Berita::create($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $berita = Berita::findOrFail($id);
        return view('admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, string $id)
    {
        $berita = Berita::findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'nullable|string|max:50',
            'location'    => 'nullable|string|max:150',
            'author'      => 'nullable|string|max:100',
            'paragraphs'  => 'nullable|string',
            'is_featured' => 'boolean',
            'image'       => 'nullable|image|max:4096',
        ]);

        $data['paragraphs']  = $this->parseParagraphs($request->input('paragraphs'));
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'news');
        } else {
            unset($data['image']);
        }

        $berita->update($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $berita = Berita::findOrFail($id);
        $berita->comments()->delete();
        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }

    // ==================== Helper ====================

    private function parseParagraphs(?string $raw): array
    {
        if (!$raw) return [];
        return array_values(array_filter(
            array_map('trim', explode("\n", $raw))
        ));
    }

    private function uploadImage($file, string $folder): string
    {
        $dir = public_path('images/' . $folder);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'images/' . $folder . '/' . $filename;
    }

    public function show(string $id) {}
}
