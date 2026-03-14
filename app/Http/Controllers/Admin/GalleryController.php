<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        // Lists for filter dropdowns/pills
        $years = GalleryItem::selectRaw('year')->distinct()->orderBy('year')->pluck('year')->all();
        $currentYear  = !empty($years) ? $years[count($years) - 1] : (int) date('Y');
        $selectedYear = (int) $request->query('year', $currentYear);

        $monthsForYear = GalleryItem::where('year', $selectedYear)
            ->distinct()->orderBy('month')->pluck('month')->all();

        $selectedMonth = $request->query('month', 'all');

        $query = GalleryItem::where('year', $selectedYear)
            ->orderBy('month')->orderBy('uploaded_at');
        if ($selectedMonth !== 'all') {
            $query->where('month', (int) $selectedMonth);
        }

        $itemsByMonth = $query->get()->groupBy('month');
        $totalCount   = $query->count();

        return view('admin.gallery.index', compact(
            'itemsByMonth', 'years', 'selectedYear', 'monthsForYear', 'selectedMonth', 'totalCount'
        ));
    }

    public function destroy(string $id)
    {
        $item = GalleryItem::findOrFail($id);
        $fullPath = public_path($item->path);
        if (is_file($fullPath)) @unlink($fullPath);

        $item->delete();
        return redirect()->back()->with('success', 'Foto berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $items = GalleryItem::whereIn('id', $request->ids)->get();

        foreach ($items as $item) {
            $fullPath = public_path($item->path);
            if (is_file($fullPath)) @unlink($fullPath);
            $item->delete();
        }

        return redirect()->back()->with('success', count($items) . ' foto berhasil dihapus.');
    }
}

