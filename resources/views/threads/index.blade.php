<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a0c12">
    <title>GameHub — Gamingforum</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gh-page">
    @include('threads.partials.header')

    <main class="gh-container gh-main">
        <section class="gh-hero">
            <h1 class="gh-title">GameHub forum</h1>
            <p class="gh-description">Bespreek games, stel vragen en deel tips met andere spelers.</p>
        </section>

        @if (session('success'))
            <p class="gh-alert">{{ session('success') }}</p>
        @endif

        <div class="gh-section-heading">
            <h2>Categorieën</h2>
            <span class="gh-muted">{{ $threads->count() }} {{ $threads->count() === 1 ? 'categorie' : 'categorieën' }}</span>
        </div>

        @if ($threads->isNotEmpty())
            <div class="gh-list">
                @foreach ($threads as $thread)
                    <article class="gh-card">
                        <a class="gh-card-link" href="{{ route('threads.show', $thread) }}">
                            <div class="gh-card-top">
                                <h2>{{ $thread->title }}</h2>
                                <span class="gh-count">{{ $thread->topics_count }} {{ $thread->topics_count === 1 ? 'onderwerp' : 'onderwerpen' }}</span>
                            </div>
                            <p class="gh-category-description">{{ $thread->description }}</p>
                            <span class="gh-link">Bekijk categorie <span aria-hidden="true">&rarr;</span></span>
                        </a>
                        @if (auth()->user()?->is_admin)
                            <form class="gh-delete-form" action="{{ route('threads.destroy', $thread) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze categorie en alle bijbehorende onderwerpen wilt verwijderen?')">
                                @csrf
                                @method('DELETE')
                                <button class="gh-button gh-button-danger" type="submit">Categorie verwijderen</button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <p class="gh-empty">Er zijn nog geen categorieën. Kom later nog eens kijken!</p>
        @endif

        @if (auth()->user()?->is_admin)
            <section class="gh-panel">
                <h2>Nieuwe categorie</h2>
                <form class="gh-form" action="{{ route('threads.store') }}" method="POST">
                    @csrf
                    <label for="title">Naam</label>
                    <input class="gh-input" id="title" name="title" value="{{ old('title') }}" placeholder="Bijvoorbeeld: PC gaming" required>
                    @error('title') <p class="gh-error">{{ $message }}</p> @enderror
                    <label for="description">Korte beschrijving</label>
                    <input class="gh-input" id="description" name="description" value="{{ old('description') }}" placeholder="Waar gaat deze categorie over?" required>
                    @error('description') <p class="gh-error">{{ $message }}</p> @enderror
                    <button class="gh-button" type="submit">Categorie toevoegen</button>
                </form>
            </section>
        @endif
    </main>

    <footer class="gh-footer">GameHub — een plek voor spelers.</footer>
</body>
</html>
