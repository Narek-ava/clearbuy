<div>
    @if (session()->has('error'))
    <x-common.alert.error>{{ session('error') }}</x-common.alert.error>
    @endif
    <x-common.table.table>
        <x-slot name="thead">
            <x-common.table.th>Agent*</x-common.table.th>
            <x-common.table.th>Current price*</x-common.table.th>
            <x-common.table.th>Original price</x-common.table.th>
            <x-common.table.th>Currency*</x-common.table.th>
            <x-common.table.th>URL*</x-common.table.th>
            <x-common.table.th>Recommended?</x-common.table.th>
            <x-common.table.th></x-common.table.th>
        </x-slot>

        @foreach ($product_prices as $index => $price)
        @continue($price === null)
       
        <x-common.table.tr>
            <x-common.table.td class="align-top">
                @livewire('agent-autocomplete', ['name' => 'product_prices['.$index.'][agent_id]', 'is_retailer'=>true,
                'item' => $price->agent_id ?? null], key('agent_'.$index))
                @isset($price->id)
                    <x-common.input.input type="hidden" value="{{$price->id}}" name="product_prices[{{ $index }}][id]" />
                @endisset
            </x-common.table.td>
            <x-common.table.td class="align-top">
                <x-common.input.input type="number" min="0" step=".01" name="product_prices[{{ $index }}][current_msrp]"
                    value="{{ $price->current_msrp }}" />
            </x-common.table.td>
            <x-common.table.td class="align-top">
                <x-common.input.input type="number" min="0" step=".01" name="product_prices[{{ $index }}][original_msrp]"
                    value="{{ $price->original_msrp }}" />
            </x-common.table.td>
            <x-common.table.td class="align-top">
                @livewire('currency-autocomplete', ['name' => 'product_prices['.$index.'][currency_id]', 'item' =>
                $price->currency_id ?? null ], key('currency_'.$index))
            </x-common.table.td>
            <x-common.table.td class="align-top">
                <x-common.input.input type="text" name="product_prices[{{ $index }}][url]" value="{{ $price->url }}" />
            </x-common.table.td>
            <x-common.table.td class="align-top">
                <label class="checkbox__control">
                    <input type="checkbox" @isset($price->recommended) {{$price->recommended ? 'checked' : '' }} @endisset name="product_prices[{{$index}}][recommended]" value="true">
                    <div class="checkbox__control__indicator"></div>
                </label>
            </x-common.table.td>
            <x-common.table.td class="align-top">
                <x-common.a.a href="#" wire:click.prevent="remove({{ $index }})" class="text-red-500">delete
                </x-common.a.a>
            </x-common.table.td>
        </x-common.table.tr>

        @endforeach
    </x-common.table.table>

    <x-common.button.group class="mt-2">
        <a href="#" class="w-full add-more-btn plus" wire:click.prevent="add"></a>
        @if($showScrappeButton)
        <button wire:click.prevent="scrap" class="w-full add-more-btn">
            <svg wire:loading wire:target="scrap" class="w-5 h-5 mr-3 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            <span wire:loading.remove wire:target="scrap">Add Amazon Price Tracking</span>
        </button>
        @endif
    </x-common.button.group>
</div>
