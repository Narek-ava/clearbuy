<x-custom-layout>

    <x-slot name="title">
        {{ $item !== null ? 'Edit Deal' : 'Add new Deal' }}
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Deals', 'list_path' => $backUrl, 'is_copy' => $is_copy, 'item'=> $item ])

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

    <form class="editItemForm font-grotesk" action="" method="post" x-data="{}"
        @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ (!$is_copy && $item !== null) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-form.container>
            <x-form.input>
                <x-slot name="label">
                    Product *
                </x-slot>
                <div class="flex items-center space-x-3" x-data="{selectedProduct:'', change: function(e){this.selectedProduct = e.target.value}}">
                    <x-common.input.select name="product_id" :required="true" class="flex-1 searchable wide" id="product_id"
                    :selected="old('product') !== null ? old('product') : ($item !== null && $item->product !== null ? $item->product->id : null)"
                    x-on:change="change"
                    :options="($products->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name . ' (' . ($item->variant->name ?? 'Default') . ')'];
                        })->toArray())" />

                    <a x-show="{{isset($item->product) ?: "selectedProduct != '' && selectedProduct != '-- select --' "}}" target="_blank"  x-bind:href="`/admin/product?id=${selectedProduct || '{{isset($item->product) ? $item->product->id : ''}}'}&backUrl={{ urlencode($backUrl) }}`" class="flex items-center h-10 p-3 space-x-1 font-bold text-gray-500 bg-white border shadow-md hover:shadow-none">
                        Open product
                        <svg class="ml-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.8333 15.8333H4.16667V4.16667H10V2.5H4.16667C3.72464 2.5 3.30072 2.67559 2.98816 2.98816C2.67559 3.30072 2.5 3.72464 2.5 4.16667V15.8333C2.5 16.2754 2.67559 16.6993 2.98816 17.0118C3.30072 17.3244 3.72464 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V10H15.8333V15.8333ZM11.6667 2.5V4.16667H14.6583L6.46667 12.3583L7.64167 13.5333L15.8333 5.34167V8.33333H17.5V2.5H11.6667Z" fill="#708195"/>
                        </svg>
                    </a>
                </div>

            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Agent *
                </x-slot>
                <x-common.input.select name="agent_id" id="agent_id" :required="true" class="w-full h-11"
                    :selected="old('agent') !== null ? old('agent') : ($item !== null && $item->agent !== null ? $item->agent->id : null)"
                    :options="($agents->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())" />
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Price *
                </x-slot>
                <div class="flex items-center space-x-3">
                    <x-common.input.input
                        type="number"
                        name="price"
                        value="{{ (old('price') !== null) ? (old('price')) : (($item != null) ? ($item->price) : '')}}"
                        placeholder="99.99" class="flex-1"/>
                </div>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Original Price
                </x-slot>
                <div class="flex items-center space-x-3">
                    <x-common.input.input
                        type="number" name="original_price"
                        value="{{ (old('original_price', $item->original_price ?? '')) }}"
                        placeholder="99.99" class="flex-1"
                    />
                </div>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Currency *
                </x-slot>
                <x-common.input.select
                    name="currency_id"
                    :required="true" class="h-11"
                    :selected="old('currency_id', $item->currency->id ?? null)"
                    :options="($currencies->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())"
                />
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    URL *
                </x-slot>
                <x-common.input.input type="text" name="url"
                    value="{{ (old('url') !== null) ? (old('url')) : (($item != null) ? ($item->url) : '')}}"
                    placeholder="https://www.amazon.co.uk/"/>
            </x-form.input>

            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span class="px-4 py-2 font-bold">Is free?</span>
                    <x-common.input.switch name="is_free" value="1"
                    :checked="(old('is_free') !== null) ? ((old('is_free')) ? 'true' : 'false') : (($item !== null && $item->is_free == true) ? 'true' : 'false')" />
                </label>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Coupon code
                </x-slot>
                <x-common.input.input type="text" name="coupon_code"
                    value="{{ (old('coupon_code') !== null) ? (old('coupon_code')) : (($item != null) ? ($item->coupon_code) : '')}}"
                    placeholder="GET50OFF"/>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Retailer custom text
                </x-slot>
                <x-common.input.input type="text" name="retailer_custom_text"
                    value="{{ (old('retailer_custom_text') !== null) ? (old('retailer_custom_text')) : (($item != null) ? ($item->retailer_custom_text) : '')}}"
                    placeholder="Retailer custom text"/>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Creation date *
                </x-slot>
                <x-common.input.input type="datetime-local" name="created_at"
                    value="{{ old('created_at') ?: (($item !== null && $item->creation_date) ? $item->creation_date : '') }}"/>
            </x-form.input>

            <x-form.input>
                <x-slot name="label">
                    Expiry date *
                </x-slot>
                <x-common.input.input type="datetime-local" name="expiry_date"
                    value="{{ old('expiry_date') ?: (($item !== null && $item->expiry_date) ? $item->expiry_date : '') }}"/>
            </x-form.input>

            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span class="px-4 py-2 font-bold">Is this deal recommended?</span>
                    <x-common.input.switch name="recommended" value="1"
                    :checked="(old('recommended') !== null) ? ((old('recommended')) ? 'true' : 'false') : (($item !== null && $item->recommended == true) ? 'true' : 'false')" />
                </label>
            </x-form.input>

            <x-form.input>
                <label class="flex items-center space-x-1">
                    <span class="px-4 py-2 font-bold">Is hot?</span>
                    <x-common.input.switch name="is_hot" value="1"
                    :checked="(old('is_hot') !== null) ? ((old('is_hot')) ? 'true' : 'false') : (($item !== null && $item->is_hot == true) ? 'true' : 'false')" />
                </label>
            </x-form.input>

        </x-form.container>
    </form>
</x-custom-layout>
