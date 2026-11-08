<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\Topic;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::withCount('topics')->latest()->get();

        return view('threads.index', compact('threads'));
    }

    public function show(Thread $thread)
    {
        $thread->load('topics.user', 'topics.replies.user');

        return view('threads.show', compact('thread'));
    }

    public function storeThread(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->threads()->create($validated);

        return redirect()->route('threads.index')->with('success', 'De forumcategorie is aangemaakt.');
    }

    public function destroyThread(Request $request, Thread $thread)
    {
        abort_unless($request->user()->is_admin, 403);

        $thread->delete();

        return redirect()->route('threads.index')->with('success', 'De categorie en bijbehorende onderwerpen zijn verwijderd.');
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

    public function updateTopic(Request $request, Thread $thread, Topic $topic)
    {
        abort_unless($topic->thread_id === $thread->id, 404);
        abort_unless($topic->user_id === $request->user()->id || $request->user()->is_admin, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $topic->update($validated);

        return redirect()->route('threads.show', $thread)->with('success', 'Je onderwerp is aangepast.');
    }

    public function destroyTopic(Request $request, Thread $thread, Topic $topic)
    {
        abort_unless($topic->thread_id === $thread->id, 404);
        abort_unless($request->user()->is_admin, 403);

        $topic->delete();

        return redirect()->route('threads.show', $thread)->with('success', 'Het onderwerp en de reacties zijn verwijderd.');
    }

    public function storeReply(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $topic->replies()->create([
            'body' => $validated['body'],
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('threads.show', $topic->thread_id)
            ->with('success', 'Je reactie is geplaatst.');
    }

    public function updateReply(Request $request, Topic $topic, Reply $reply)
    {
        abort_unless($reply->topic_id === $topic->id, 404);
        abort_unless($reply->user_id === $request->user()->id || $request->user()->is_admin, 403);

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $reply->update($validated);

        return redirect()
            ->route('threads.show', $topic->thread_id)
            ->with('success', 'Je reactie is aangepast.');
    }

    public function destroyReply(Request $request, Topic $topic, Reply $reply)
    {
        abort_unless($reply->topic_id === $topic->id, 404);
        abort_unless($request->user()->is_admin, 403);

        $reply->delete();

        return redirect()->route('threads.show', $topic->thread_id)->with('success', 'De reactie is verwijderd.');
    }
}
