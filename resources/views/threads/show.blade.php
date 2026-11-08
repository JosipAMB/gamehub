<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a0c12">
    <title>{{ $thread->title }} — GameHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gh-page">
    @include('threads.partials.header')

    <main class="gh-container gh-main">
        <a class="gh-back" href="{{ route('threads.index') }}">&larr; Alle categorieën</a>

        <section class="gh-hero">
            <h1 class="gh-title">{{ $thread->title }}</h1>
            <p class="gh-description">{{ $thread->description }}</p>
        </section>

        @if (session('success'))
            <p class="gh-alert">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="gh-alert" role="alert">
                Controleer je bericht en probeer het opnieuw.
            </div>
        @endif

        <div class="gh-section-heading">
            <div>
                <h2>Onderwerpen</h2>
            </div>
            <span class="gh-muted">{{ $thread->topics->count() }} {{ $thread->topics->count() === 1 ? 'onderwerp' : 'onderwerpen' }}</span>
        </div>

        @if ($thread->topics->isNotEmpty())
            <div class="gh-list">
                @foreach ($thread->topics as $topic)
                    <article class="gh-card">
                        <div class="gh-card-top">
                            <div>
                                <h2>{{ $topic->title }}</h2>
                                <p class="gh-meta">Gestart door {{ $topic->user->username }}</p>
                            </div>
                            <span class="gh-count">{{ $topic->replies->count() }} {{ $topic->replies->count() === 1 ? 'reactie' : 'reacties' }}</span>
                        </div>
                        <p>{{ $topic->body }}</p>

                        @if (auth()->user()?->id === $topic->user_id || auth()->user()?->is_admin)
                            <details class="gh-details">
                                <summary>Onderwerp aanpassen</summary>
                                <form class="gh-form" action="{{ route('topics.update', [$thread, $topic]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <label for="topic-title-{{ $topic->id }}">Titel</label>
                                    <input class="gh-input" id="topic-title-{{ $topic->id }}" name="title" value="{{ $topic->title }}" required>
                                    <label for="topic-body-{{ $topic->id }}">Bericht</label>
                                    <textarea class="gh-input gh-textarea" id="topic-body-{{ $topic->id }}" name="body" required>{{ $topic->body }}</textarea>
                                    <button class="gh-button gh-button-secondary" type="submit">Wijzigingen opslaan</button>
                                </form>
                            </details>
                        @endif
                        @if (auth()->user()?->is_admin)
                            <form class="gh-delete-form" action="{{ route('topics.destroy', [$thread, $topic]) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je dit onderwerp en alle reacties wilt verwijderen?')">
                                @csrf
                                @method('DELETE')
                                <button class="gh-button gh-button-danger" type="submit">Onderwerp verwijderen</button>
                            </form>
                        @endif

                        <div class="gh-replies">
                            <h4>Reacties</h4>
                            @forelse ($topic->replies as $reply)
                                <div class="gh-reply">
                                    <p class="gh-meta">{{ $reply->user->username }}</p>
                                    <p>{{ $reply->body }}</p>
                                    @if (auth()->user()?->id === $reply->user_id || auth()->user()?->is_admin)
                                        <details class="gh-details">
                                            <summary>Reactie aanpassen</summary>
                                            <form class="gh-form" action="{{ route('replies.update', [$topic, $reply]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <label for="reply-edit-{{ $reply->id }}">Bericht</label>
                                                <textarea class="gh-input gh-textarea" id="reply-edit-{{ $reply->id }}" name="body" rows="3" required>{{ $reply->body }}</textarea>
                                                <button class="gh-button gh-button-secondary" type="submit">Wijzigingen opslaan</button>
                                            </form>
                                        </details>
                                    @endif
                                    @if (auth()->user()?->is_admin)
                                        <form class="gh-delete-form" action="{{ route('replies.destroy', [$topic, $reply]) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze reactie wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="gh-button gh-button-danger" type="submit">Reactie verwijderen</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="gh-muted">Nog geen reacties. Begin het gesprek!</p>
                            @endforelse

                            @auth
                                <form class="gh-form" action="{{ route('replies.store', $topic) }}" method="POST">
                                    @csrf
                                    <label for="reply-{{ $topic->id }}">Jouw reactie</label>
                                    <textarea class="gh-input gh-textarea" id="reply-{{ $topic->id }}" name="body" rows="3" placeholder="Wat wil je toevoegen?" required></textarea>
                                    @error('body') <p class="gh-error">{{ $message }}</p> @enderror
                                    <button class="gh-button gh-button-secondary" type="submit">Reactie plaatsen</button>
                                </form>
                            @else
                                <p class="gh-muted">
                                    <a href="{{ route('login') }}">Log in om mee te praten</a>
                                    · <a href="{{ route('register') }}">Nog geen account? Registreren</a>
                                </p>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="gh-empty">Nog geen onderwerpen in deze categorie. Wees de eerste die iets vraagt!</p>
        @endif

        @auth
            <section class="gh-panel">
                <h2>Nieuw onderwerp</h2>
                <form class="gh-form" action="{{ route('topics.store', $thread) }}" method="POST">
                    @csrf
                    <label for="title">Titel</label>
                    <input class="gh-input" id="title" name="title" value="{{ old('title') }}" placeholder="Waar wil je over praten?" required>
                    @error('title') <p class="gh-error">{{ $message }}</p> @enderror
                    <label for="body">Bericht</label>
                    <textarea class="gh-input gh-textarea" id="body" name="body" placeholder="Deel je vraag of idee met de community..." required>{{ old('body') }}</textarea>
                    @error('body') <p class="gh-error">{{ $message }}</p> @enderror
                    <button class="gh-button" type="submit">Onderwerp plaatsen</button>
                </form>
            </section>
        @else
            <section class="gh-panel">
                <h2>Praat mee met de community</h2>
                <p class="gh-description">Log in om een onderwerp te starten of te reageren.</p>
                <a class="gh-button" href="{{ route('login') }}">Inloggen</a>
                <a class="gh-button gh-button-secondary" href="{{ route('register') }}">Nog geen account? Registreren</a>
            </section>
        @endauth
    </main>

    <footer class="gh-footer">GameHub — een plek voor spelers.</footer>
</body>
</html>
