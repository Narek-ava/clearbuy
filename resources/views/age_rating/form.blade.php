<x-custom-layout>

    <x-slot name="title">
        Age rating
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Age ratings', 'list_path' => '/admin/age_ratings', 'is_copy' => $is_copy, 'item'=> $item ])

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
                <x-slot name="label">
                    Minimal age *
                </x-slot>
                <x-common.input.input type="number" name="age_from"
                value="{{ (old('age_from') !== null) ? (old('age_from')) : (($item != null) ? ($item->age_from) : 0)}}" />
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
