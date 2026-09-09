<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class JournalController extends Controller
{
    public function index()
    {
        return view('journal.index', [
            'posts' => BlogPost::where('published', true)->latest('published_at')->get(),
        ]);
    }

    public function show(BlogPost $blogPost)
    {
        abort_unless($blogPost->published, 404);

        return view('journal.show', ['post' => $blogPost]);
    }
}
