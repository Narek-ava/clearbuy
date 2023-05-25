<x-custom-layout>

    <x-slot name="title">
        Currency
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Currencies', 'list_path' => '/admin/currencies', 'is_copy' => $is_copy, 'item'=> $item ])

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

    @livewire('importable.currencies-import', ['exportUrl'=> route('export-currencies')])

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
                    Symbol *
                </x-slot>
                <x-common.input.input type="text" name="symbol"
                value="{{ (old('symbol') !== null) ? (old('symbol')) : (($item != null) ? ($item->symbol) : '')}}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Country *
                </x-slot>
                <x-common.input.select-multiple
                    name="country_ids[]"
                    id="countries"
                    :required="true"
                    :selected="$errors->any() ? old('country') : ($item !== null && $item->country_ids !== null ? $item->country_ids : null)"
                    :options="($countries->map(function($item) {
                                return (object)['key' => $item->id, 'value' => $item->name];
                            })->toArray())"
                />
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
