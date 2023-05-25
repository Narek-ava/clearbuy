<div class="mb-5 overflow-y-auto">
    <div class="variant_title">Variant:</div>
    <div class="variant_list">

        {{-- Default variant (parent product) --}}

        <div class="variant_item">
            <input type="radio" name="variant_id" value="" id="var_radio_default" @if(is_null($parent_id)) {{ $checked }} @endif>
            <label for="var_radio_default"
                  @if(!is_null($parent_id))
                        @click="document.getElementById('next-variant').value = 'default';
                        $dispatch('draft');
                        $dispatch('submit-save-form')"
                  @endif

                    >
                    <span class="tab_label_title">Default</span>
            </label>
        </div>

        {{-- other variants (children) --}}

        @if($items->isNotEmpty())
            @foreach($items as $item)
                <div class="variant_item" x-data="data({{ json_encode($item) }})">

                    <input type="radio" name="variant_id" value="{{ $item->id }}" id="var_radio_{{ $item->id }}" {{ $item->checked ?? '' }} >

                    <label for="var_radio_{{ $item->id }}" x-show="!isEditing"

                        @if(!isset($item->checked))
                            @click="document.getElementById('next-variant').value = {{ $item->product_id }};
                            $dispatch('draft');
                            $dispatch('submit-save-form')"
                        @endif

                        >
                            <span class="tab_label_title" x-html="variant_icons"></span>
                    </label>

                    <div class="tab_label_edit" x-show="isEditing">
                        <input type="text"
                               x-model="itemname"
                               @input.lazy="Livewire.emit('set-itemname', itemname)"
                               @click.away="disableEditing(); @this.rename({{ $item->id }})"
                               @keydown.enter="disableEditing(); @this.rename({{ $item->id }})"
                               @keydown.window.escape="disableEditing"
                               wire:model="itemname"
                               x-ref="input" class="label_rename" x-cloak >

                        <div class="checkmark" wire:click="rename({{ $item->id }})" x-cloak></div>
                    </div>

                </div>
            @endforeach
        @endif

        {{-- new variant form --}}

        @if(is_null($parent_id))
            <div x-data="{ open: false }" @close-input.window="open = false" id="variant_addition">
                <span class="plus_icon_btn" @click="open = true;" ></span>
                <div x-show="open" id="variant_new_item" x-cloak>
                    <input type="text" placeholder="type new..." id="varriant_new_input" class="new_input_item" wire:model.lazy="itemnew" value="{{ $itemnew }}" >
                </div>
            </div>
        @endif
    </div>



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

</div>


@once
    @push('footerScripts')
        <script>
            function data(item) {

                var variant_icons = item.checked ?
                           '<span x-text="itemname" class="itemname"></span>'
                          +'<span class="edit_icon_btn mx-2" @click="toggleEditingState(); Livewire.emit(\'set-itemname\', itemname);" ></span>'
                          +'<span class="trash_icon_btn" @click="$dispatch(\'delete-selected\', { id: '+item.product_id+', action: \'delete_products\' })" ></span>' :
                           '<span x-text="itemname" class="itemname"></span>';

                return {
                    itemname: item.name,
                    variant_icons: variant_icons,
                    isEditing: false,
                    toggleEditingState() {
                        this.isEditing = !this.isEditing;

                        if (this.isEditing) {
                            this.$nextTick(() => {
                                this.$refs.input.focus();
                            });
                        }
                    },
                    disableEditing() {
                        this.isEditing = false;
                    }
                };
            }

        </script>
    @endpush
@endonce
