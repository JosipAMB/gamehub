<!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GameHub - Gaming Forum</title>
    </head>
    <body>
        <h1>GameHub</h1>
        <p>Welkom op het gamingforum voor gamers!</p>

        <h2>Forumcategorieën</h2>

        @forelse ($threads as $thread)
            <article>
                <h3>{{ $thread->title }}</h3>

                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $thread->description }}
                </p>
            </article>
        @empty
            <p>Er zijn nog geen forumcategorieën aangemaakt.</p>
        @endforelse
    </body>
</html>