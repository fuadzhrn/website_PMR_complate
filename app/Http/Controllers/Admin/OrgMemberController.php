<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrgMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrgMemberController extends Controller
{
    public function index()
    {
        $periods        = OrgMember::select('period')->distinct()->orderByDesc('period')->pluck('period');
        $selectedPeriod = request('period', $periods->first());
        $members        = OrgMember::where('period', $selectedPeriod)->orderBy('sort_order')->get();
        return view('admin.org.index', compact('members', 'periods', 'selectedPeriod'));
    }

    public function create()
    {
        $periods  = OrgMember::select('period')->distinct()->orderByDesc('period')->pluck('period');
        $pengurus = OrgMember::where('role_group', 'pengurus')->orderByDesc('period')->orderBy('sort_order')->get();
        return view('admin.org.create', compact('pengurus', 'periods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'name'         => 'required|string|max:150',
            'domisili'     => 'required|string|max:150',
            'role_group'   => 'required|in:pengurus,staf',
            'position_key' => 'nullable|string|max:100',
            'parent_key'   => 'nullable|string|max:100',
            'sort_order'   => 'nullable|integer',
            'period'       => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'angkatan'     => 'nullable|integer|min:1',
            'photo'        => 'nullable|image|max:4096',
        ]);

        if ($data['role_group'] === 'pengurus') {
            if (empty($data['position_key'])) {
                return back()->withErrors(['position_key' => 'Pilih posisi di bagan untuk pengurus.'])->withInput();
            }
            $data['parent_key'] = null;
        } else {
            // Staf: auto-generate position_key, abaikan posisi bagan
            $data['position_key'] = Str::slug($data['title']) . '-' . Str::random(4);
        }

        // Always compute on server to keep value consistent and tamper-proof.
        $data['angkatan'] = $this->resolveAngkatanFromPeriod($data['period']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        OrgMember::create($data);
        return redirect()->route('admin.org.index', ['period' => $data['period']])
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $member   = OrgMember::findOrFail($id);
        $periods  = OrgMember::select('period')->distinct()->orderByDesc('period')->pluck('period');
        $pengurus = OrgMember::where('role_group', 'pengurus')->orderByDesc('period')->orderBy('sort_order')->get();
        return view('admin.org.edit', compact('member', 'pengurus', 'periods'));
    }

    public function update(Request $request, string $id)
    {
        $member = OrgMember::findOrFail($id);

        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'name'         => 'required|string|max:150',
            'domisili'     => 'required|string|max:150',
            'role_group'   => 'required|in:pengurus,staf',
            'position_key' => 'nullable|string|max:100',
            'parent_key'   => 'nullable|string|max:100',
            'sort_order'   => 'nullable|integer',
            'period'       => ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/'],
            'angkatan'     => 'nullable|integer|min:1',
            'photo'        => 'nullable|image|max:4096',
        ]);

        if ($data['role_group'] === 'pengurus') {
            if (empty($data['position_key'])) {
                return back()->withErrors(['position_key' => 'Pilih posisi di bagan untuk pengurus.'])->withInput();
            }
            $data['parent_key'] = null;
        } else {
            $data['position_key'] = $member->position_key ?: Str::slug($data['title']) . '-' . Str::random(4);
        }

        // Always compute on server to keep value consistent and tamper-proof.
        $data['angkatan'] = $this->resolveAngkatanFromPeriod($data['period']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        } else {
            unset($data['photo']);
        }

        $member->update($data);
        return redirect()->route('admin.org.index', ['period' => $data['period']])
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        OrgMember::findOrFail($id)->delete();
        return redirect()->route('admin.org.index')->with('success', 'Anggota berhasil dihapus.');
    }

    private function uploadPhoto($file): string
    {
        $dir = public_path('images/struktur');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'images/struktur/' . $filename;
    }

    public function show(string $id) {}

    private function resolveAngkatanFromPeriod(string $period): int
    {
        if (!preg_match('/^(\d{4})-(\d{4})$/', $period, $m)) {
            return 1;
        }

        $startYear = (int) $m[1];
        return max(1, $startYear - 2002 + 1);
    }
}

