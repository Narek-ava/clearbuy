<x-custom-layout>

    <x-slot name="title">
        Role
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Users', 'list_path' => '/admin/users', 'is_copy' => $is_copy, 'item'=> $item ])

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
                value="{{ $errors->any() ? (old('name')) : (($item !== null) ? ($item->name) : '') }}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Email *
                </x-slot>
                <x-common.input.input type="email" name="email"
                value="{{ $errors->any() ? (old('email')) : (($item !== null) ? ($item->email) : '') }}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Password *
                </x-slot>
                <x-common.input.input type="password" name="password"/>
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Confirm password *
                </x-slot>
                <x-common.input.input type="password" name="password_confirmation"/>
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Roles *
                </x-slot>
                <div class="grid grid-cols-2 gap-1 gap-x-4 md:grid-cols-3">
                    @foreach ($roles as $role)
                        <label class="flex items-center space-x-1">
                            <div class="mr-1 checkbox__control">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                {{
                                    (old('roles') !== null) ?
                                    (collect(old('roles'))->contains($role->id) ? 'checked' : '') :
                                    (($item !== null && $item->roles->contains($role)) ? 'checked' : '')
                                }}
                            >
                                <div class="checkbox__control__indicator"></div>
                            </div>
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
            </x-form.input>
            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span class="px-4 py-2 font-bold">Get product requests email notifications</span>
                    <x-common.input.switch name="product_request_mailing" value="1"
                    :checked="(old('product_request_mailing') !== null) ?
                                (old('product_request_mailing') ? 'true' : 'false') :
                                (($item !== null && $item->product_request_mailing == true) ? 'true' : 'false')" />
                </label>
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
