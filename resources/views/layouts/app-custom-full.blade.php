<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    {{-- <link rel="stylesheet" href="{{ asset('css/nice-select2.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        .drag-file {

            background: #F7F7F8;
            border: 1px solid #D2D2D2;
            box-sizing: border-box;
            border-radius: 3px;
            padding-top: 37px;
            padding-bottom: 37px;
            width: 100%;
        }
    </style>

    @stack('scripts')

    @livewireStyles

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script>

    {{-- <script src="{{ asset('js/nice-select2.js') }}" defer></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" ></script>


</head>

<body class="antialiased font-proxima">
    <div class="flex flex-row flex-no-wrap min-h-screen overflow-auto bg-white">
        <main class="flex-grow" style="min-width: 700px;background-color:#fafafa;">
            <header class="flex justify-between p-6 m-1 rounded-lg header-shadow item-center">
                <img src="/imgs/logo-dark.svg" alt="ClearBuy"/>
                @livewire('navigation-dropdown')
            </header>
            <div class="grid gap-4 p-8 mx-auto">

                {{ $slot }}

            </div>
        </main>
    </div>
    <!-- Modals -->
    @stack('modals')

    @livewireScripts

    @stack('footerScripts')
</body>

</html>
