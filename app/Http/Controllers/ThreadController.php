<?php

namespace App\Http\Controllers;

use App\Models\Thread;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::latest()->get();

        return view('threads.index', compact('threads'));
    }
}