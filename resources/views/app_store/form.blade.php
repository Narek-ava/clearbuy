<x-custom-layout>

    <x-slot name="title">
        App Store
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks/>
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'App stores', 'list_path' => '/admin/app_stores', 'is_copy' => $is_copy, 'item'=> $item ])

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

    {{-- @livewire('importable.agents-import', ['exportUrl'=> route('export-agents')]) --}}

    <form class="editItemForm" action="" method="post" x-data="{}"
          @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ isset($item) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-form.container>
            <x-form.input>
                <x-slot name="label">
                    App Store Name *
                </x-slot>
                <x-common.input.input type="text" name="name"
                                      value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '')}}"/>
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Owner of App Store *
                </x-slot>
                <x-common.input.select
                    name="brand"
                    id="brand"
                    :required="true"
                    :selected="old('brand') !== null ? old('brand') : ($item !== null && $item->brand !== null ? $item->brand->id : null)"
                    :options="($brands->map(function($item) {
                        return (object)['key' => $item->id, 'value' => $item->name];
                    })->toArray())"
                />
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    URL *
                </x-slot>
                <x-common.input.input type="text" name="url"
                                      value="{{ (old('url') !== null) ? (old('url')) : (($item != null) ? ($item->url) : '')}}"/>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Icon
                </x-slot>
                <x-common.input.input type="text" name="icon"
                    value="{{ (old('icon') !== null) ? (old('icon')) : (($item != null) ? ($item->icon) : '')}}"/>
            </x-form.input>

        </x-form.container>
    </form>
</x-custom-layout>
