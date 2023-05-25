<div class="flex">

    <x-form.container-row>
        @if ($this->groups->isEmpty())
            <x-form.input>
                Selected category has no attributes of this kind
            </x-form.input>
        @else

            @foreach($this->groups->split(2) as $row)
                <div class="m-15 mt-0 w-1/3" >
                    @foreach ($row as $group)

                        @if(!$group->repeatable)

                            <div class="mb-2 group-name-header">{{ $group->name}}</div>

                            @foreach ($group->attributes as $attribute)
                                <x-form.input>
                                    <div class="group-name-header-label">
                                        {{ $attribute->name }}
                                    </div>
                                    @php
                                        $val = $attribute->ValueForProduct($productId);
                                    @endphp

                                    @switch(intval($attribute->type))

                                        @case(0)    {{-- numeric --}}

                                            <x-common.input.input type="number" name="product_attributes[{{ $attribute->id }}]"
                                                              style="width: 83.333333%;"
                                                              value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" />
                                            @break

                                        @case(1)    {{-- string --}}

                                            <x-common.input.input type="text" name="product_attributes[{{ $attribute->id }}]"
                                                              value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" />
                                            @break

                                        @case(2)    {{-- boolean --}}

                                            <div class="flex flex-row items-center space-x-2 flex-no-wrap product-specification-radio">
                                                <label class="space-x-1 whitespace-no-wrap mr-3">
                                                    <input type="radio"
                                                           {{ $old !== null && isset($old[$attribute->id])
                                                            ? ($old[$attribute->id] !== null && $old[$attribute->id] ? 'checked' : '')
                                                            : ($val !== null && $val ? 'checked' : '') }}
                                                           name="product_attributes[{{ $attribute->id }}]" value="1"><span>Yes</span>
                                                </label>
                                                <label class="space-x-1 whitespace-no-wrap">
                                                    <input type="radio"
                                                           {{ $old !== null && isset($old[$attribute->id])
                                                            ? ($old[$attribute->id] !== null && !$old[$attribute->id] ? 'checked' : '')
                                                            : ($val !== null && !$val ? 'checked' : '') }}
                                                           name="product_attributes[{{ $attribute->id }}]" value="0"><span>No</span>
                                                </label>
                                            </div>
                                            @break

                                        @case(3)    {{-- datetime --}}

                                            <x-common.input.input type="date" name="product_attributes[{{ $attribute->id }}]"
                                                              value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" />

                                            @break

                                        @case(4)    {{-- single option --}}

                                            <x-common.input.select
                                                name="product_attributes[{{ $attribute->id }}]"
                                                id="product_attributes_{{ $attribute->id }}"
                                                :required="true"
                                                selected="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : ($val !== null ? $val->id : '') }}"
                                                :options="($attribute->options !== null ? $attribute->options->map(function($item) {
                                                    return (object)['key' => $item->id, 'value' => $item->name];
                                                })->toArray() : [])"
                                            />
                                            @break

                                        @case(5)    {{-- multiple options --}}

                                            @livewire('product-option-autocomplete', [
                                                'name' => 'product_attributes_multiple['.$attribute->id.'][]',
                                                'attr' => $attribute,
                                                'items' => ($old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val)
                                            ], key('product_attribute_'.$attribute->id))
                                            @break

                                        @case(6)    {{-- decimal (rating) --}}



                                            <table class="rating_input_tbl">

                                                <tr>
                                                    <td class="w-1/6">
                                                        <input type="text" name="product_attributes[{{ $attribute->id }}]" id="slh_{{$attribute->id}}" value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" class="rating_input" wire:ignore/>
                                                    </td>
                                                    <td class="w-5/6">
                                                        <div class="slide_item mb-5 mt-2" id="sl_{{$attribute->id}}"></div>
                                                    </td>
                                                </tr>
                                            </table>


                                            <script>
                                                var attrId  = @json($attribute->id);
                                                var attrVal = @json($val ?? 5);

                                                noUiSlider.create(document.getElementById('sl_'+attrId), {
                                                    start: [attrVal],
                                                    step: 1,
                                                    range: {
                                                        'min': [1],
                                                        'max': [10]
                                                    },
                                                    connect: [true, false],
                                                    pips: {
                                                        mode: 'steps',
                                                        density: 10,
                                                        filter: (value, type) => {
                                                            return 0; //no pips values
                                                        }
                                                    }
                                                }).on('update', function (values, handle) {
                                                    document.getElementById('slh_'+attrId).value = Math.round(values[handle]);
                                                });

                                            </script>

                                            @break

                                        @default


                                    @endswitch

                                    @if ($attribute->measure !== null)
                                        @if($attribute->type != 6) {{-- except for the slider --}}
                                            <div class="flex product-specification-measure justify-center">{{ $attribute->measure->short_name }}</div>
                                        @endif
                                    @endif
                                </x-form.input>
                            @endforeach

                        @else {{-- repeatable --}}

                            <div x-data="attr_group_action({{ $group->id }})">

                                <div class="mb-2 group-name-header float-left mr-2">{{ $group->name}}</div>

                                {{-- new group form --}}

                                <div x-data="{ open: false }" @close-input.window="open = false" class="add_attr_set">
                                    <span class="plus_icon_btn" @click="open = true;" ></span>
                                    <div x-show="open" id="variant_new_item" x-cloak>
                                        <input type="text" class="new_input_item" placeholder="type new set name" value=""
                                            wire:keydown.enter="addSubGroup({{ $group->id }}, $event.target.value)"
                                            @keydown.enter="open = false"
                                            @click.away="open = false">
                                    </div>
                                </div>

                                <div class="tabs_for_attributes" >

                                    {{-- other groups (children) --}}

                                    @if($group->children->isNotEmpty())
                                        @foreach($group->children as $key => $child)

                                            <input type="radio" name="attr_tabs_{{ $group->id }}" id="tabon-{{ $child->id }}"

                                                @if($key==0)
                                                    checked="checked"
                                                @endif
                                            >

                                            <label for="tabon-{{ $child->id }}" x-data="attr_group_action({{ $group->id }})" @click="showEdit({{ $child->id }})" class="tabs_for_attributes_label" >

                                                <span class="tab_label_title" x-show="!isEditing">
                                                    <span class="itemname" >{{ $child->name }}</span>

                                                    <span class="edit_set_{{ $group->id }} edit_set hide" id="edit_set_{{ $child->id }}" >
                                                        <span class="edit_icon_btn mx-2" @click="toggleEditingStateGroup({{ $child }});" ></span>
                                                        <span class="trash_icon_btn" @click="$dispatch('delete-selected-attr-set', { id: {{ $child->id }}, action: 'delete_attribute_groups' })" ></span>
                                                    </span>
                                                </span>

                                                <div class="tab_label_edit" x-show="isEditing">
                                                    <input type="text"
                                                           x-model="itemname"
                                                           @click.away="disableEditingGroup()"
                                                           @keydown.enter="disableEditingGroup(); @this.rename({{ $child->id }})"
                                                           @keydown.window.escape="disableEditingGroup()"
                                                           wire:model="itemname"
                                                           x-ref="input" class="label_rename" x-cloak >

                                                           <div class="checkmark" x-show="isEditing" wire:click="rename({{ $child->id }})" x-cloak></div>
                                                </div>
                                            </label>

                                            <div class="tab">
                                                @foreach ($child->attrs as $attribute)
                                                    <x-form.input>
                                                        <div class="group-name-header-label">
                                                            {{ $attribute->name }}
                                                        </div>
                                                        @php
                                                            $val = $attribute->ValueForProduct($productId);
                                                        @endphp

                                                        @switch(intval($attribute->type))

                                                            @case(0)    {{-- numeric --}}

                                                                <x-common.input.input type="number" name="product_attributes[{{ $attribute->id }}]"
                                                                                  style="width: 83.333333%;"
                                                                                  value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" />
                                                                @break

                                                            @case(1)    {{-- string --}}

                                                                <x-common.input.input type="text" name="product_attributes[{{ $attribute->id }}]"
                                                                                  style="width: 83.333333%;"
                                                                                  value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" />
                                                                @break

                                                            @case(2)    {{-- boolean --}}

                                                                <div class="flex flex-row items-center space-x-2 flex-no-wrap product-specification-radio">
                                                                    <label class="space-x-1 whitespace-no-wrap mr-3">
                                                                        <input type="radio"
                                                                               {{ $old !== null && isset($old[$attribute->id])
                                                                                ? ($old[$attribute->id] !== null && $old[$attribute->id] ? 'checked' : '')
                                                                                : ($val !== null && $val ? 'checked' : '') }}
                                                                               name="product_attributes[{{ $attribute->id }}]" value="1"><span>Yes</span>
                                                                    </label>
                                                                    <label class="space-x-1 whitespace-no-wrap">
                                                                        <input type="radio"
                                                                               {{ $old !== null && isset($old[$attribute->id])
                                                                                ? ($old[$attribute->id] !== null && !$old[$attribute->id] ? 'checked' : '')
                                                                                : ($val !== null && !$val ? 'checked' : '') }}
                                                                               name="product_attributes[{{ $attribute->id }}]" value="0"><span>No</span>
                                                                    </label>
                                                                </div>
                                                                @break

                                                            @case(3)    {{-- datetime --}}

                                                                <x-common.input.input type="date" name="product_attributes[{{ $attribute->id }}]"
                                                                                  value="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val }}" />

                                                                @break

                                                            @case(4)    {{-- single option --}}

                                                                <x-common.input.select
                                                                    name="product_attributes[{{ $attribute->id }}]"
                                                                    id="product_attributes_{{ $attribute->id }}"
                                                                    :required="true"
                                                                    selected="{{ $old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : ($val !== null ? $val->id : '') }}"
                                                                    :options="($attribute->options !== null ? $attribute->options->map(function($item) {
                                                                        return (object)['key' => $item->id, 'value' => $item->name];
                                                                    })->toArray() : [])"
                                                                />
                                                                @break

                                                            @case(5)    {{-- multiple options --}}

                                                                @livewire('product-option-autocomplete', [
                                                                    'name' => 'product_attributes_multiple['.$attribute->id.'][]',
                                                                    'attr' => $attribute,
                                                                    'items' => ($old !== null && isset($old[$attribute->id]) ? $old[$attribute->id] : $val)
                                                                ], key('product_attribute_'.$attribute->id))
                                                                @break

                                                        @endswitch

                                                        @if ($attribute->measure !== null)
                                                            @if($attribute->type != 6) {{-- except for the slider --}}
                                                                <div class="flex product-specification-measure justify-center">{{ $attribute->measure->short_name }}</div>
                                                            @endif
                                                        @endif
                                                    </x-form.input>
                                                @endforeach
                                            </div>



                                        @endforeach
                                    @endif


                                </div>

                            </div>

                        @endif

                        <x-form.input>
                            <br>
                        </x-form.input>
                    @endforeach
                </div>
            @endforeach

        @endif
    </x-form.container-row>

    @if (session()->has('notice'))
        <x-common.alert.error>
            {{ session('notice') }}
        </x-common.alert.error>
    @endif

    @if (session()->has('message'))
        <x-common.alert.success>
            {{ session('message') }}
        </x-common.alert.success>
    @endif


    @once
        {{--
            //this styles are lost with re-rending page with LW (live saving)
            // TODO: find out this bug, temporarily transfered to add.css

            @push('headerScripts')
                <link href="{{ asset('css/nouislider.min.css') }}" rel="stylesheet"  />
            @endpush
        --}}


        @push('footerScripts')
            <script>

                function attr_group_action(parent_id) {

                    return {
                        itemname: '',
                        isEditing: false,
                        showEdit(e) {
                            let group_items = document.querySelectorAll(`.edit_set_${parent_id}`);
                            group_items.forEach(function(el){
                                if (el.classList.contains("show")) {
                                    el.classList.remove('show');
                                    el.classList.add('hide');
                                }
                            });

                            let el = document.querySelector(`#edit_set_${e}`);
                            if (el) {
                                if (el.classList.contains("hide")) {
                                    el.classList.remove('hide');
                                    el.classList.add('show');
                                }
                            }
                        },
                        toggleEditingStateGroup(item) {

                            this.itemname = item.name;

                            this.isEditing = !this.isEditing;

                            if (this.isEditing) {
                                this.$nextTick(() => {
                                    this.$refs.input.focus();
                                });
                            }
                        },
                        disableEditingGroup() {
                            this.isEditing = false;
                        }
                    };
                }

                function deleteAttrSet() {
                    return {
                        show: false,
                        item: null,
                        url:null,
                        submitDelete() {
                            Livewire.emit('deleteAttrSet', this.item);
                            this.show = false;
                        }
                    }
                }
            </script>


            <script src="{{ asset('js/nouislider.js') }}" referrerpolicy="no-referrer"></script>
        @endpush
    @endonce

</div>
