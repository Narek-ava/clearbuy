<x-custom-layout>

    <x-slot name="title">
        Allowed domains
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a type="alt-white" href="/admin/domains">Domains</x-common.button.a>
                <x-common.button.a href="#" x-data="{}" @click.prevent="$dispatch('submit-save-form')">Save changes
                </x-common.button.a>
            </x-common.button.group>

            <script>
                function deleteItemsButton() {
                        return {
                            show: false
                        }
                    }
            </script>
        </div>
    </x-slot>

        @if (session('status') == 'success')
            <x-common.alert.success>
                {{ session('message') }}
            </x-common.alert.success>
        @endif

        @if ($errors->any())
            <div class="space-y-1">
                @foreach ($errors->all() as $error)
                    <x-common.alert.error>
                        {{ $error }}
                    </x-common.alert.error>
                @endforeach
            </div>
        @endif

    <form class="editItemForm" action="" method="post" x-data="{}" @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ isset($item) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-form.container>
            <x-form.input>
                <x-slot name="label">
                    Domain *
                </x-slot>
                <x-common.input.input type="text" name="domain"
                value="{{ (old('domain') !== null) ? (old('domain')) : (($item != null) ? ($item->domain) : '')}}" />
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
