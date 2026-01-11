<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard PGO
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-2">Selamat datang, {{ auth()->user()->name }}</h3>
                <p class="text-gray-600">Anda masuk sebagai <strong>PGO</strong>.</p>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Silakan lanjutkan dengan pengelolaan izin atau data lain yang dibutuhkan.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
