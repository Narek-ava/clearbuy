<x-custom-layout>

    <x-slot name="title">
        Tag
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Tags', 'list_path' => '/admin/tags', 'is_copy' => $is_copy, 'item'=> $item ])

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

    @livewire('importable.brands-import', ['exportUrl'=> route('export-brands')])

    <form class="overflow-x-auto editItemForm" action="" method="post" x-data="{}" @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ isset($item) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-common.tabs>
            <x-slot name="General">
                <x-form.container>
                    <x-form.input>
                        <x-slot name="label">
                            Name *
                        </x-slot>
                        <x-common.input.input type="text" name="name"
                        value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '') }}" />
                    </x-form.input>
                </x-form.container>
            </x-slot>

        </x-common.tabs>
    </form>
</x-custom-layout>
