<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm font-semibold leading-tight text-gray-800 dark:text-gray-200">PERMIT LISTRIK TEGANGAN TINGGI</h2>
    </x-slot>

    @include('components.permit-mobile-style')

    <section class="bg-cover bg-center bg-no-repeat px-4 py-10" style="background-image: url('/images/bg-login.jpg');">
        <div class="wp-permit-form mx-auto max-w-5xl rounded-xl bg-white p-6 shadow-md">
            @if (session('alert'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-2 text-sm text-green-700">{{ session('alert') }}</div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-2 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-2 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('components.permits.listrik-tegangan-tinggi', [
                'formAction' => route('working-permit.listrik-tegangan-tinggi.token.store', $permit->token),
                'signaturePrefix' => 'token-ltt-'.$permit->id,
            ])

            @if (session('token_pdf_url'))
                <div class="mt-4 text-center">
                    <a href="{{ session('token_pdf_url') }}" target="_blank" class="inline-flex rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Lihat PDF</a>
                </div>
            @endif
        </div>
    </section>

    @include('components.sign-pad')
</x-app-layout>
