<div>
    <x-common.table.table>
        <x-slot name="thead">
            <x-common.table.th>App&nbsp;store&nbsp;name *</x-common.table.th>
            <x-common.table.th>Free</x-common.table.th>
            <x-common.table.th>In app purchases</x-common.table.th>
            <x-common.table.th>url *</x-common.table.th>
            <x-common.table.th>Price *</x-common.table.th>
            <x-common.table.th>Currency *</x-common.table.th>
            <x-common.table.th></x-common.table.th>
        </x-slot>
        @foreach ($links as $link)
            @continue($link === null)
            
            <x-common.table.tr x-data="showPrice()" x-init="show">
                <x-common.table.td class="align-top">
                    @livewire('app-store-autocomplete', ['name' => 'links['.$loop->index.'][store_id]', 'item' => $link->store_id], key('store_'.$loop->index))
                    @isset($link->id)
                        <x-common.input.input type="hidden" value="{{$link->id}}" name="links[{{ $loop->index }}][id]" />
                    @endisset

                    {{-- <x-common.input.input type="text" name="links[{{ $loop->index }}][app_store_name]" value="{{ $link->app_store_name }}" /> --}}
                </x-common.table.td>

                <x-common.table.td class="align-top">
                    <x-common.input.switch name="links[{{ $loop->index }}][free]" value="1" x-ref="free" x-on:click="show" class="round"
                            :checked="(old('name') !== null) ? ((old('free')) ? 'true' : 'false') : (($link !== null && $link->free == true) ? 'true' : 'false')" />
                </x-common.table.td>

                <x-common.table.td class="align-top">
                    <x-common.input.switch name="links[{{ $loop->index }}][app_purchase]" value="1" x-ref="app_purchase" x-on:click="show" class="round"
                                :checked="(old('name') !== null) ? ((old('app_purchase')) ? 'true' : 'false') : (($link !== null && $link->app_purchase == true) ? 'true' : 'false')" />
                </x-common.table.td>

                <x-common.table.td class="align-top">
                    <x-common.input.input type="text" name="links[{{ $loop->index }}][url]" value="{{ $link->url }}" />
                </x-common.table.td>

                <x-common.table.td class="align-top">
                    <template x-if="show_price">
                        <x-common.input.input type="number" min="0" name="links[{{ $loop->index }}][price]" value="{{ $link->price }}" />
                    </template>
                </x-common.table.td>

                <x-common.table.td class="align-top">
                    <div x-show="show_price">
                        @livewire('currency-autocomplete', ['name' => 'links['.$loop->index.'][currency_id]', 'item' =>
                        $link->currency_id ?? null ], key('currency_'.$loop->index))
                    </div>
                </x-common.table.td>


                <x-common.table.td class="align-top">
                    <x-common.a.a href="#" wire:click.prevent="remove({{ $loop->index }})" class="text-red-500">delete</x-common.a.a>
                </x-common.table.td>
            </x-common.table.tr>
        @endforeach
    </x-common.table.table>

    <x-common.button.group class="mt-2">
        <x-common.button.a href="#" wire:click.prevent="add">add</x-common.button.a>
    </x-common.button.group>
</div>
<script>
    function showPrice(){
        return {
            show_price:true,
            show(){
                if(!this.$refs.free.checked && !this.$refs.app_purchase.checked){
                    this.show_price = true;
                }else{
                    this.show_price = false;
                }
            }
        }
    }
</script>
