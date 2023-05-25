<x-custom-layout>

    <x-slot name="title">
        Role
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Roles', 'list_path' => '/admin/roles', 'is_copy' => $is_copy, 'item'=> $item ])

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
                value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '') }}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Permissions *
                </x-slot>
                <div class="grid grid-cols-2 gap-1 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($permissions as $permission)
                    <label class="flex items-center space-x-1">
                        <div class="mr-1 checkbox__control">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            {{
                                (old('permissions') !== null) ?
                                (collect(old('permissions'))->contains($permission->id) ? 'checked' : '') :
                                (($item !== null && $item->permissions->contains($permission)) ? 'checked' : '')
                            }}
                        >
                            <div class="checkbox__control__indicator"></div>
                        </div>
                        {{ $permission->name }}
                    </label>
                    @endforeach
                </div>
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
