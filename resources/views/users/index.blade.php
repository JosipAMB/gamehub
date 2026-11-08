<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gebruikers beheren
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <p class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</p>
            @endif

            @forelse ($users as $user)
                <article class="p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                        <a class="underline hover:text-gray-700 dark:hover:text-gray-300"
                           href="{{ route('users.show', $user) }}">
                            {{ $user->username ?: $user->name }}
                        </a>
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Aangemaakt: {{ $user->created_at }}</p>
                </article>
            @empty
                <p class="p-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg text-gray-600 dark:text-gray-400">
                    Er zijn nog geen gebruikers.
                </p>
            @endforelse
        </div>
    </div>
</x-app-layout>
