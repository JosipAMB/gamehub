<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::latest()->get();

        return view('threads.index', compact('threads'));
    }

    public function show(Thread $thread)
    {
        $thread->load('topics.user');

        return view('threads.show', compact('thread'));
    }
    
    public function storeTopic(Request $request, Thread $thread)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $thread->topics()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('threads.show', $thread)
            ->with('success', 'Je onderwerp is geplaatst!');
    }
}