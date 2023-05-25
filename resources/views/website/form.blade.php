<x-custom-layout>

    <x-slot name="title">
        Website
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Websites', 'list_path' => '/admin/websites', 'is_copy' => $is_copy, 'item'=> $item ])

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

    @livewire('importable.websites-import', ['exportUrl'=> route('export-websites')])

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
                    URL *
                </x-slot>
                <x-common.input.input type="text" name="url"
                value="{{ $errors->any() ? (old('url')) : (($item != null) ? ($item->url) : '')}}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Description
                </x-slot>
                <x-common.input.textarea-limited limit="500" name="description"
                value="{{ $errors->any() ? (old('description')) : (($item != null) ? ($item->description) : '')}}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Logo
                </x-slot>
                @livewire('item-images', [
                    'name' => 'logo',
                    'multiple' => false,
                    'path' => 'websites',
                    'images' => is_null($item) ? [] : (is_null($item->logo) ? [] : $item->logo)
                ])
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
