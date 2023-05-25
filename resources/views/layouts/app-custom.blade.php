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
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add.css') }}">

    @livewireStyles

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    @stack('headerScripts')

</head>

<body class="antialiased font-proxima">
    <div class="flex flex-row flex-no-wrap min-h-screen overflow-auto bg-white">
        <aside class="flex-shrink-0 text-sm font-black w-72" style="background-color: #1E1E1E">
            <div class="grid w-full h-24 place-items-center ">
                <img src="/imgs/logo.svg" alt="The Database">
            </div>
            {{ $sidebarLinks }}
        </aside>
        <main class="flex-grow" style="min-width: 700px;background-color:#fafafa;">
            <header class="flex justify-between p-3 m-1 rounded-lg h-15 header-shadow item-center">
                {{ $top }}
                @livewire('navigation-dropdown')
            </header>
            <div class="grid gap-4 p-4  mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
    <!-- Modals -->
    @stack('modals')

    <div style="display: none;" x-data="deleteItemsModal()" x-show="show"
         x-on:show-delete-items-modal.window="showModal"
         class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemsModal sm:py-28">
        <div @click.away="show = false"
             class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
                <span @click="show = false"
                      class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10 ">
                <p class="text-lg font-semibold">Delete selected items? </p>
                <x-common.button.group class="flex flex-row flex-no-wrap items-center justify-center mt-10 space-x-2">
                    <x-common.button.a href="#" type="alt" @click.prevent="show = false">
                        Cancel
                    </x-common.button.a>
                    <x-common.button.a href="#" @click.prevent="submitDelete">
                        Delete
                    </x-common.button.a>
                </x-common.button.group>
            </div>

        </div>
    </div>
    <script>
        function deleteItemsModal() {
            return {
                show: false,
                submitDelete() {
                    document.querySelector('form.deleteItemsForm').submit()
                },
                showModal() {
                    if ([...document.querySelectorAll('form.deleteItemsForm input[type=checkbox][name]:checked')].length > 0) {
                        this.show = true;
                    }
                }
            }
        }
    </script>

    {{-- Delete individual item --}}
    <div style="display: none;" x-data="deleteItemModal()" x-show="show"
         @delete-selected.window="item = $event.detail.id; url = $event.detail.action ?? null; show=true"
         class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemModal sm:py-28">
        <div @click.away="show = false"
             class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
                <span @click="show = false"
                      class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10 ">
                <p class="text-lg font-semibold">Delete selected item? </p>
                <x-common.button.group class="flex flex-row flex-no-wrap items-center justify-center mt-10 space-x-2">
                    <x-common.button.a href="#" type="alt" @click.prevent="show = false">
                        Cancel
                    </x-common.button.a>
                    <x-common.button.a href="#" @click.prevent="submitDelete">
                        Delete
                    </x-common.button.a>
                </x-common.button.group>
            </div>

        </div>
    </div>
    <script>
        function deleteItemModal() {
            return {
                show: false,
                item: null,
                url:null,
                submitDelete() {
                    if(document.querySelector('form.deleteItemsForm')){
                        this.url = document.querySelector('form.deleteItemsForm').action;
                    }
                    var csrf = document.querySelector('meta[name="csrf-token"]').content;
                    fetch(this.url.replace(' ', '_').toLowerCase(), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({items: [this.item]})
                    }).then( () => {
                        var params = new URLSearchParams(window.location.search);
                        if(params.get('backUrl')!=null){
                            window.location.href = params.get('backUrl');
                        }else{
                            location.reload()
                        }
                    })
                },
                showModal() {
                    if (this.item != null) {
                        this.show = true;
                    }
                }
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if(!document.getElementById('search')) return;
            var $rows = document.querySelectorAll('.table > tbody tr.group');
            if ($rows.length) {
                document.getElementById('search').addEventListener('keyup', function (e) {
                    var filter = this.value.trim().replace(/ +/g, ' ').toLowerCase();
                    for (i = 0; i < $rows.length; i++) {
                        var rowContent = $rows[i].textContent;
                        rowContent = rowContent.replace(/[\s]+/g, ' ');
                        if (rowContent) {
                            if (rowContent.toLowerCase().includes(filter)) {
                                $rows[i].style.display = "";
                            } else {
                                $rows[i].style.display = "none";
                            }
                        }
                    }
                });
            }
        });
    const changeUrlParamWithReload = (paramName, paramValue) => {
        let params = new URLSearchParams(window.location.search);
        params.set(paramName, paramValue);
        document.location = `${window.location.pathname}?${params.toString()}`;
    }
    </script>

    @livewireScripts

    @stack('footerScripts')

</body>

</html>