<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('berita')->latest()->paginate(20);
        return view('admin.comment.index', compact('comments'));
    }

    public function destroy(string $id)
    {
        Comment::findOrFail($id)->delete();
        return redirect()->route('admin.comment.index')->with('success', 'Komentar berhasil dihapus.');
    }
}

