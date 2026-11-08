<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gebruiker beheren
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Aangemaakt: {{ $user->created_at }} · Laatst aangepast: {{ $user->updated_at }}
            </p>

            @if (session('success'))
                <p class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</p>
            @endif

            <section class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Accountgegevens</h3>

                    <form class="mt-6 space-y-6" action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" value="Naam" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="username" value="Gebruikersnaam" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                                :value="old('username', $user->username)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('username')" />
                        </div>

                        <div>
                            <x-input-label for="email" value="E-mailadres" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <input type="hidden" name="is_admin" value="0">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="is_admin" value="1"
                                    @checked(old('is_admin', $user->is_admin))
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                Beheerder
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('is_admin')" />
                        </div>

                        <x-primary-button>Wijzigingen opslaan</x-primary-button>
                    </form>
                </div>
            </section>

            @if (! $user->is(auth()->user()))
                <section class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Account verwijderen</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Het account en de bijbehorende forumactiviteiten worden verwijderd.
                        </p>

                        <form class="mt-6" action="{{ route('users.destroy', $user) }}" method="POST"
                            onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>Gebruiker verwijderen</x-danger-button>
                        </form>

                        <x-input-error class="mt-2" :messages="$errors->get('user')" />
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
