<x-custom-layout>

    <x-slot name="title">
        Agent
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks/>
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Agents', 'list_path' => '/admin/agents', 'is_copy' => $is_copy, 'item'=> $item ])

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

    @livewire('importable.agents-import', ['exportUrl'=> route('export-agents')])

    <form class="editItemForm" action="" method="post" x-data="agentForm()"
          @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ isset($item) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-form.container>
            <x-form.input>
                <x-slot name="label">
                    Type *
                </x-slot>
                <x-common.input.select
                    name="type_id"
                    @change="typeChange"
                    id="type"
                    :required="true"
                    :selected="old('type') !== null ? old('type') : ($item !== null ? $item->type->id : null)"
                    :options="($types->map(function($item, $index) {
                        return (object)['key' => $index, 'value' => $item];
                    })->toArray())"
                />
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    <span x-text="nameLabel"></span>
                </x-slot>
                <x-common.input.input type="text" name="name"
                                      value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '')}}"/>
            </x-form.input>
            <x-form.input x-show="showLastName">
                <x-slot name="label">
                    Last name
                </x-slot>
                <x-common.input.input type="text" name="surname"
                                      value="{{ (old('surname') !== null) ? (old('surname')) : (($item != null) ? ($item->surname) : '')}}"/>
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Website
                </x-slot>
                <x-common.input.input type="text" name="website"
                                      value="{{ (old('website') !== null) ? (old('website')) : (($item != null) ? ($item->website) : '')}}"/>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Country *
                </x-slot>
                <x-common.input.select-multiple
                    name="countries[]"
                    id="countries"
                    size="5"
                    :required="true"
                    :selected="old('countries') !== null ? old('countries') : ($item !== null ? $item->countries : null)"
                    :options="$countries->map(function($item) {
                        return (object)['key' => $item->id, 'value' => $item->name];
                    })"
                />
            </x-form.input>

            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span class="px-4 py-2 font-bold">Is retailer *</span>
                    <div class="mr-1 checkbox__control">
                        <input type="checkbox" name="is_retailer" value="1"
                        {{ (old('name') !== null) ? ((old('is_retailer')) ? 'checked' : '') : (($item !== null && $item->is_retailer) ? 'checked' : '') }}>
                        <div class="checkbox__control__indicator"></div>
                    </div>
                </label>
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Logo
                </x-slot>
                @livewire('item-images', [
                'name' => 'images[]',
                'path' => 'agents',
                'multiple' => false,
                'images' => ($errors->any() ? (old('image') ? [old('image')] : []) : ($item !== null && $item->image !==
                null ? [$item->image] : []))])
            </x-form.input>
        </x-form.container>
    </form>
    <script>
        function agentForm() {
            return {

                nameLabel: 'Name *',
                showLastName: false,
                typeChange($event) {
                    if ($event.target.value == 0 || isNaN($event.target.value)) {
                        this.nameLabel = 'Name *';
                        this.showLastName = false;
                    } else if ($event.target.value == 1) {
                        this.nameLabel = 'First name *';
                        this.showLastName = true;
                    }
                }
            }
        }
    </script>
</x-custom-layout>
