<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeContentController extends Controller
{
    public function index()
    {
        $sections     = HomeContent::where('section', 'not like', 'hero-slide-%')->get()->keyBy('section');
        $slideRecords = HomeContent::where('section', 'like', 'hero-slide-%')->orderBy('section')->get();
        return view('admin.home-content.index', compact('sections', 'slideRecords'));
    }

    public function update(Request $request)
    {
        // Hapus satu slide jika tombol Hapus ditekan
        if ($request->has('delete_slide')) {
            $slideId = (int) $request->input('delete_slide');
            $slide   = HomeContent::find($slideId);
            if ($slide && str_starts_with($slide->section, 'hero-slide-')) {
                if ($slide->image && is_file(public_path($slide->image))) {
                    @unlink(public_path($slide->image));
                }
                $slide->delete();
            }
            return redirect()->route('admin.home-content.index')->with('success', 'Slide berhasil dihapus.');
        }

        $validation = [
            'selayang_title'          => 'required|string|max:255',
            'selayang_content'        => 'required|string',
            'selayang_image'          => 'nullable|image|max:4096',
            'tentang_title'           => 'required|string|max:255',
            'tentang_content'         => 'required|string',
            'tentang_image'           => 'nullable|image|max:4096',
            'hero_slide_replace'      => 'nullable|array',
            'hero_slide_replace.*'    => 'nullable|image|max:8192',
            'hero_slide_new'          => 'nullable|array',
            'hero_slide_new.*'        => 'nullable|image|max:8192',
        ];
        $data = $request->validate($validation);

        $sections = [
            'selayang-pandang' => [
                'title'   => $data['selayang_title'],
                'content' => $data['selayang_content'],
            ],
            'tentang-kami' => [
                'title'   => $data['tentang_title'],
                'content' => $data['tentang_content'],
            ],
        ];

        foreach (['selayang' => 'selayang-pandang', 'tentang' => 'tentang-kami'] as $prefix => $sectionKey) {
            $field = $prefix . '_image';
            $record = HomeContent::firstOrNew(['section' => $sectionKey]);
            $record->title   = $sections[$sectionKey]['title'];
            $record->content = $sections[$sectionKey]['content'];

            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $dir  = public_path('images/home');
                if (!is_dir($dir)) mkdir($dir, 0755, true);

                $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $filename);
                $record->image = 'images/home/' . $filename;
            }

            $record->save();
        }

        // Ganti gambar slide yang sudah ada
        if ($request->hasFile('hero_slide_replace')) {
            foreach ($request->file('hero_slide_replace') as $id => $file) {
                $record = HomeContent::find((int) $id);
                if (!$record || !str_starts_with($record->section, 'hero-slide-')) continue;

                if ($record->image && is_file(public_path($record->image))) {
                    @unlink(public_path($record->image));
                }
                $dir = public_path('images/home');
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename      = 'slide_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $filename);
                $record->image = 'images/home/' . $filename;
                $record->save();
            }
        }

        // Tambah slide baru
        if ($request->hasFile('hero_slide_new')) {
            // Cari nomor urut tertinggi yang sudah ada
            $maxN = HomeContent::where('section', 'like', 'hero-slide-%')
                ->get()
                ->map(fn($r) => (int) str_replace('hero-slide-', '', $r->section))
                ->max() ?? 0;

            $dir = public_path('images/home');
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            foreach ($request->file('hero_slide_new') as $file) {
                if (!$file || !$file->isValid()) continue;
                $maxN++;
                $filename = 'slide' . $maxN . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $filename);

                HomeContent::create([
                    'section' => 'hero-slide-' . $maxN,
                    'title'   => 'Hero Slide ' . $maxN,
                    'content' => '',
                    'image'   => 'images/home/' . $filename,
                ]);
            }
        }

        return redirect()->route('admin.home-content.index')->with('success', 'Konten beranda berhasil disimpan.');
    }
}

