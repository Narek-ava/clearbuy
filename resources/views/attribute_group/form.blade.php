<x-custom-layout>

    <x-slot name="title">
        Attribute group
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Attribute groups', 'list_path' => '/admin/attribute_groups', 'is_copy' => $is_copy, 'item'=> $item ])



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
                value="{{ $errors->any() ? (old('name')) : (($item != null) ? ($item->name) : '') }}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Sort order
                </x-slot>
                <x-common.input.input type="number" name="sort_order"
                value="{{ $errors->any() ? (old('sort_order')) : (($item != null) ? ($item->sort_order) : 0) }}" />
            </x-form.input>

            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span class="px-4 py-2 font-bold">Repeatable</span>
                    <x-common.input.switch name="repeatable" value="1"
                    :checked="(old('repeatable') !== null) ? ((old('repeatable')) ? 'true' : 'false') : (($item !== null && $item->repeatable == true) ? 'true' : 'false')" />
                </label>
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
