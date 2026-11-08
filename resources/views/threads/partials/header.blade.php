<header class="gh-header">
    <div class="gh-container gh-nav">
        <a class="gh-brand" href="{{ route('threads.index') }}">
            <span>GameHub</span>
        </a>

        <nav class="gh-nav-links" aria-label="Hoofdnavigatie">
            @auth
                <span class="gh-nav-user">Hoi, {{ auth()->user()->username ?: auth()->user()->name }}</span>
                <a href="{{ route('profile.edit') }}">Profiel</a>
                @if (auth()->user()->is_admin)
                    <a href="{{ route('users.index') }}">Gebruikers</a>
                @endif
                <form class="gh-form-inline" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="gh-nav-button" type="submit">Uitloggen</button>
                </form>
            @else
                <a href="{{ route('login') }}">Inloggen</a>
                <a href="{{ route('register') }}">Registreren</a>
            @endauth
        </nav>
    </div>
</header>
