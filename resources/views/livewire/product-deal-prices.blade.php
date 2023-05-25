<div>
    <x-common.table.table>
        <x-slot name="thead">
            <x-common.table.th>Agent*</x-common.table.th>
            <x-common.table.th>Current price*</x-common.table.th>
            <x-common.table.th>Original price</x-common.table.th>
            <x-common.table.th>Currency*</x-common.table.th>
            <x-common.table.th>URL*</x-common.table.th>
            <x-common.table.th>Coupon Code</x-common.table.th>
            <x-common.table.th>Retailer custom text</x-common.table.th>
            <x-common.table.th>Expiry date*</x-common.table.th>
            <x-common.table.th>Recommended?</x-common.table.th>
            <x-common.table.th>Hot deal?</x-common.table.th>
            <x-common.table.th>Is free?</x-common.table.th>
            <x-common.table.th></x-common.table.th>
        </x-slot>

        @foreach ($deal_prices as $index => $deal)
            @continue($deal === null)
            <x-common.table.tr>
                <x-common.table.td class="align-top">
                    @livewire('agent-autocomplete', ['name' => 'deal_prices['.$index.'][agent_id]', 'is_retailer'=>true, 'item' => $deal->agent_id], key('agent_'.$index))
                    @isset($deal->id)
                        <x-common.input.input type="hidden" value="{{$deal->id}}" name="deal_prices[{{ $index }}][id]" />
                    @endisset
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="number" min="0" name="deal_prices[{{ $index }}][price]" value="{{ $deal->price }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="number" min="0" name="deal_prices[{{ $index }}][original_price]" value="{{ $deal->original_price }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    @livewire('currency-autocomplete', ['name' => 'deal_prices['.$index.'][currency_id]', 'item' => $deal->currency_id], key('currency_'.$index))
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="text" name="deal_prices[{{ $index }}][url]" value="{{ $deal->url }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="text" name="deal_prices[{{ $index }}][coupon_code]" value="{{ $deal->coupon_code }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="text" name="deal_prices[{{ $index }}][retailer_custom_text]" value="{{ $deal->retailer_custom_text }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="datetime-local" name="deal_prices[{{ $index }}][expiry_date]" value="{{ $deal->expiry_date ?? '' }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <label class="checkbox__control">
                        <input type="checkbox" @isset($deal->recommended) {{ $deal->recommended ? 'checked' : '' }} @endisset name="deal_prices[{{$index}}][recommended]" value="true">
                        <div class="checkbox__control__indicator"></div>
                    </label>
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <label class="checkbox__control">
                        <input type="checkbox" {{ $deal->is_hot ? 'checked' : '' }} name="deal_prices[{{ $index }}][is_hot]" value="true">
                        <div class="checkbox__control__indicator"></div>
                    </label>
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <label class="checkbox__control">
                        <input type="checkbox" {{ $deal->is_free ? 'checked' : '' }} name="deal_prices[{{ $index }}][is_free]" value="true">
                        <div class="checkbox__control__indicator"></div>
                    </label>
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.a.a href="#" wire:click.prevent="remove({{ $index }})" class="text-red-500">delete</x-common.a.a>
                </x-common.table.td>
            </x-common.table.tr>
        @endforeach
    </x-common.table.table>

    <x-common.button.group class="mt-2">
        <a href="#" class="w-full add-more-btn plus" wire:click.prevent="add"></a>
    </x-common.button.group>
</div>
