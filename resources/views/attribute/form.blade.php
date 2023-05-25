<x-custom-layout>

    <x-slot name="title">
        Attribute
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Attributes', 'list_path' => '/admin/attributes', 'is_copy' => $is_copy, 'item'=> $item ])

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

    @php
        $disabled = $item && $item->group->product_id ? true : false;
    @endphp


    <form class="editItemForm" action="" method="post" x-data="{}" @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ isset($item) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-form.container x-data="attributeForm()" x-init="setDefaultOptions">
            <x-form.input>
                <x-slot name="label">
                    Name *
                </x-slot>
                <x-common.input.input type="text" name="name"
                value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '')}}" />
            </x-form.input>
            <x-form.input>
                <x-slot name="label">
                    Measure unit
                </x-slot>
                <x-common.input.select
                    name="measure"
                    @change="typeChange"
                    id="measure"
                    :required="true"
                    :selected="old('measure') !== null ? old('measure') : ($item ? $item->measure_id : null)"
                    :options="($measures->map(function($item, $index) {
                        return (object)['key' => $item->id, 'value' => $item->name];
                    })->toArray())"
                />
            </x-form.input>

            @if ($disabled)

                <x-form.input>
                    <x-slot name="label">
                        Type *
                    </x-slot>
                    <x-common.input.select
                        name="type"
                        @change="typeChange"
                        id="type"
                        :required="true"
                        :selected="old('type') !== null ? old('type') : ($item ? $item->type : null)"
                        :options="($types->map(function($item, $index) {
                            return (object)['key' => $index, 'value' => $item];
                        })->toArray())"

                        disabled
                    />
                    <input type="hidden" name="type" value="{{ $item->type }}" />
                </x-form.input>
                <x-form.input>
                    <x-slot name="label">
                        Kind *
                    </x-slot>
                    @foreach ($kinds as $kind_id => $kind_name)
                        <label class="radio__control">
                            <input type="radio" name="kind" value="{{ $kind_id }}" disabled
                            {{
                                $errors->any() ?
                                (old('kind') == $kind_id ? 'checked' : '') :
                                (($item !== null && $item->kind == $kind_id) ? 'checked' : '')
                            }}
                        ><div class="radio__control__indicator"></div> {{ $kind_name }}
                        </label>
                    @endforeach
                    <input type="hidden" name="kind" value="{{ $item->kind }}" />
                </x-form.input>
                <x-form.input>
                    <x-slot name="label">
                        Group *
                    </x-slot>
                    <x-common.input.select
                        name="group"
                        id="group"
                        :required="true"
                        :selected="$errors->any() ? old('group') : ($item !== null && $item->group !== null ? $item->group->id : null)"
                        :options="($groups->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name.' ('.($item->product_id ?? 'common').')'];
                        })->toArray())"

                        disabled
                    />
                    <input type="hidden" name="group" value="{{ $item->group->id }}" />
                </x-form.input>

            @else

                <x-form.input>
                    <x-slot name="label">
                        Type *
                    </x-slot>
                    <x-common.input.select
                        name="type"
                        @change="typeChange"
                        id="type"
                        :required="true"
                        :selected="old('type') !== null ? old('type') : ($item ? $item->type : null)"
                        :options="($types->map(function($item, $index) {
                            return (object)['key' => $index, 'value' => $item];
                        })->toArray())"

                    />
                </x-form.input>
                <x-form.input>
                    <x-slot name="label">
                        Kind *
                    </x-slot>
                    @foreach ($kinds as $kind_id => $kind_name)
                        <label class="radio__control">
                            <input type="radio" name="kind" value="{{ $kind_id }}"
                            {{
                                $errors->any() ?
                                (old('kind') == $kind_id ? 'checked' : '') :
                                (($item !== null && $item->kind == $kind_id) ? 'checked' : '')
                            }}
                        ><div class="radio__control__indicator"></div> {{ $kind_name }}
                        </label>
                    @endforeach
                </x-form.input>
                <x-form.input>
                    <x-slot name="label">
                        Group *
                    </x-slot>
                    <x-common.input.select
                        name="group"
                        id="group"
                        :required="true"
                        :selected="$errors->any() ? old('group') : ($item !== null && $item->group !== null ? $item->group->id : null)"
                        :options="($groups->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name.' ('.($item->product_id ?? 'common').')'];
                        })->toArray())"
                    />
                </x-form.input>

            @endif


            <x-form.input>
                <x-slot name="label">
                    Sort order
                </x-slot>
                <x-common.input.input type="number" name="sort_order"
                value="{{ $errors->any() ? (old('sort_order')) : (($item != null) ? ($item->sort_order) : 0) }}" />
            </x-form.input>
            <x-form.input x-show="showOptions">
                <x-slot name="label">
                    Options
                </x-slot>
                <div class="pb-2 space-y-2 optionsBlock">
                    @if (old('options') !== null)
                        @foreach (old('options') as $key => $option)
                            <x-common.button.group>
                                <x-common.input.input type="text" name="options[{{ preg_match('/^id_[0-9]+$/', $key) ? $key : '' }}]" value="{{ $option }}" />
                                <x-common.a.a href="#" class="text-red-500" @click="removeOption">Delete</x-common.a.a>
                            </x-common.button.group>
                        @endforeach
                    @elseif ($item !== null)
                        @foreach ($item->options as $option)
                            <x-common.button.group>
                                <x-common.input.input type="text" name="options[id_{{ $option->id }}]" value="{{ $option->name }}" />
                                <x-common.a.a href="#" class="text-red-500" @click="removeOption">Delete</x-common.a.a>
                            </x-common.button.group>
                        @endforeach
                    @endif
                </div>
                <x-common.button.a href="#" title="Ctrl + Q" @click="addOption" class="addOptionButton">More</x-common.button.a>
            </x-form.input>
        </x-form.container>
    </form>

    @push('footerScripts')
        <script>

            var default_type = @json( old('type') !== null ? old('type') : ($item !== null ? $item->type : 0) );

            function attributeForm() {
                return {
                    type: default_type,
                    showOptions: false,
                    setDefaultOptions() {
                        this.showOptions = this.type == 4 || this.type == 5;
                    },
                    typeChange() {
                        this.type = document.querySelector('#type').value;
                        this.setDefaultOptions();
                    },
                    removeOption($event) {
                        $event.target.parentElement.remove();
                    },
                    addOption() {
                        document.querySelector(".optionsBlock").insertAdjacentHTML("beforeend", `
                        <x-common.button.group>
                            <x-common.input.input type="text" name="options[]" value="" />
                            <x-common.a.a href="#" class="text-red-500" @click="removeOption">Delete</x-common.a.a>
                        </x-common.button.group>
                        `);
                    }
                }
            }

            document.addEventListener("DOMContentLoaded", function() {
                document.addEventListener("keypress", function(e) {
                    if (e.ctrlKey && e.code == "KeyQ") {
                        document.querySelector(".addOptionButton").click();
                    }
                });
            });
        </script>
    @endpush
</x-custom-layout>
