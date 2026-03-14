<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::latest()->paginate(10);
        return view('admin.program.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.program.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'nullable|date_format:Y-m-d',
            'location'    => 'nullable|string|max:150',
            'author'      => 'nullable|string|max:100',
            'status'      => 'required|in:selesai,berlangsung,akan-datang',
            'intro'       => 'nullable|string',
            'paragraphs'  => 'nullable|string',
            'has_report'  => 'boolean',
            'image'       => 'nullable|image|max:4096',
            'report_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if (!empty($data['date'])) {
            $carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date']);
            $data['month'] = $carbon->month;
            $data['year']  = $carbon->year;
        }

        $data['slug']       = Str::slug($data['title']) . '-' . Str::random(5);
        $data['paragraphs'] = $this->parseParagraphs($request->input('paragraphs'));
        $data['has_report'] = $request->boolean('has_report');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'program');
        }

        if ($request->hasFile('report_file')) {
            $data['report_file'] = $this->uploadPdf($request->file('report_file'));
        } else {
            unset($data['report_file']);
        }

        Program::create($data);

        return redirect()->route('admin.program.index')->with('success', 'Program berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $program = Program::findOrFail($id);
        return view('admin.program.edit', compact('program'));
    }

    public function update(Request $request, string $id)
    {
        $program = Program::findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'nullable|date_format:Y-m-d',
            'location'    => 'nullable|string|max:150',
            'author'      => 'nullable|string|max:100',
            'status'      => 'required|in:selesai,berlangsung,akan-datang',
            'intro'       => 'nullable|string',
            'paragraphs'  => 'nullable|string',
            'has_report'  => 'boolean',
            'image'       => 'nullable|image|max:4096',
            'report_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if (!empty($data['date'])) {
            $carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $data['date']);
            $data['month'] = $carbon->month;
            $data['year']  = $carbon->year;
        }

        $data['paragraphs'] = $this->parseParagraphs($request->input('paragraphs'));
        $data['has_report'] = $request->boolean('has_report');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'program');
        } else {
            unset($data['image']);
        }

        if ($request->hasFile('report_file')) {
            // Hapus file lama jika ada
            if ($program->report_file && is_file(public_path($program->report_file))) {
                @unlink(public_path($program->report_file));
            }
            $data['report_file'] = $this->uploadPdf($request->file('report_file'));
        } else {
            unset($data['report_file']);
        }

        $program->update($data);

        return redirect()->route('admin.program.index')->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Program::findOrFail($id)->delete();
        return redirect()->route('admin.program.index')->with('success', 'Program berhasil dihapus.');
    }

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

    private function uploadPdf($file): string
    {
        $dir = public_path('laporan');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = time() . '_' . Str::random(8) . '.pdf';
        $file->move($dir, $filename);

        return 'laporan/' . $filename;
    }

    public function show(string $id) {}
}
