<!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $thread->title }} - GameHub</title>
    </head>
    <body>
        <h1>GameHub</h1>

        <h2>{{ $thread->title }}</h2>
        <p>{{ $thread->description }}</p>

        <hr>

        <h2>Onderwerpen</h2>

        @forelse ($thread->topics as $topic)
            <article>
                <h3>{{ $topic->title }}</h3>
                <p>Gestart door: {{ $topic->user->name }}</p>
                <p>{{ $topic->body }}</p>
            </article>
            <hr>
        @empty
            <p>Er zijn nog geen onderwerpen geplaatst.</p>
        @endforelse

        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        @auth
            <hr>
            <h2>Nieuw onderwerp plaatsen</h2>

            <form action="{{ route('topics.store', $thread) }}" method="POST">
                @csrf
                <label for="title">Titel:</label>
                <br>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>

                @error('title')
                    <p>{{ $message }}</p>
                @enderror

                <br><br>

                <label for="body">Bericht:</label>
                <br>
                <textarea id="body" name="body" rows="5" required>{{ old('body') }}</textarea>

                @error('body')
                    <p>{{ $message }}</p>
                @enderror

                <br><br>

                <button type="submit">Onderwerp plaatsen</button>
            </form>
        @else
            <p>Log in om een onderwerp te plaatsen.</p>
        @endif

        <a href="{{ route('threads.index') }}">
            Terug naar de homepage
        </a>
    </body>
</html>