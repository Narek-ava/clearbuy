<x-custom-layout>

    <x-slot name="title">
        License type
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Licenses', 'list_path' => '/admin/licenses', 'is_copy' => $is_copy, 'item'=> $item ])

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
                    Name *
                </x-slot>
                <x-common.input.input type="text" name="name"
                value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '')}}" />
            </x-form.input>
            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span>Is open source</span>
                    <div class="checkbox__control">
                        <input type="checkbox" name="is_open_source" value="1"
                        {{ (old('name') !== null) ? ((old('is_open_source')) ? 'checked' : '') : (($item !== null && $item->is_open_source) ? 'checked' : '') }}>
                        <div class="checkbox__control__indicator"></div>
                    </div>
                </label>
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
