<x-custom-full-layout>

    <x-slot name="title">
        Product request
    </x-slot>

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

    <div class="mx-auto product-request-form lg:w-7/12 font-grotesk">
        <h3 class="pt-10 pb-6 text-4xl font-bold text-gray-500">Product request Form</h3>
        <form class="editItemForm" action="" method="post" enctype='multipart/form-data'
            x-data="{retailer: '{{ old('agent_id') !== null ? old('agent_id') : null }}', asin:'', priceTracking: false}"
            x-init="$watch('asin', value => {value=='' ? priceTracking = false : ''})"
            @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
            @csrf
            <input type="hidden" name="id" value="{{ (!$is_copy && $item !== null) ? $item->id : ''}}">
            <input type="hidden" name="backUrl" value="{{ $backUrl }}">
            <x-form.container class="px-20 py-20 bg-white lg:w-full rounded-2xl box-shadow">
                <x-form.input class="input">
                    <div class="flex items-center justify-between px-4 ">
                        <div class="text-2xl font-bold text-black ">Product Info</div>
                    </div>
                </x-form.input>
                <x-form.input>
                    <x-slot name="label">
                        Product name*
                    </x-slot>
                    <div class="flex items-center space-x-3">
                        <x-common.input.input name="name" :required="true" class="py-2" placeholder="Samsung Galaxy S21 Ultra"
                            value="{{ old('name') !== null ? old('name') : ($item !== null && $item->name !== null ? $item->name->id : null) }}" />
                    </div>

                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        Brand
                    </x-slot>
                    <div class="flex items-center space-x-3">
                        <x-common.input.select name="brand" id="brands" class="flex-1 py-2 wide searchable"
                            :selected="old('brand') !== null ? old('brand') : ($item !== null && $item->brand !== null ? $item->brand->id : null)"
                            x-on:change="change" :options="($brands->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())" />
                    </div>

                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        Reasons to buy
                    </x-slot>
                    <x-common.input.input name="reasons_to_buy" class="py-2" placeholder="Reason 1 | Reason 2 | Reason 3"
                        value="{{ (old('reasons_to_buy') !== null) ? (old('reasons_to_buy')) : (($item != null) ? ($item->reasons_to_buy) : '') }}" />
                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        Excerpt
                    </x-slot>
                    <x-common.input.input name="excerpt" class="py-2" placeholder="A one sentence overview of the project"
                        value="{{ (old('excerpt') !== null) ? (old('excerpt')) : (($item != null) ? ($item->excerpt) : '') }}" />
                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        Summary
                    </x-slot>
                   <textarea class="block w-full px-2 px-4 py-3 border rounded-md resize-none"
                   name="summary_main"
                   rows="5"
                   value="{{ (old('summary_main') !== null) ? (old('summary_main')) : (($item != null) ? ($item->summary_main) : '')}}"
                   placeholder="A slightly more in-depth breakdown of the product’s core features and positives."></textarea>
                </x-form.input>

                <x-form.input class="price-input">
                    <x-slot name="label">
                        MSRP
                    </x-slot>
                    <div class="flex items-center space-x-3">
                        <x-common.input.select name="currency_msrp" id="currency_msrp" default="" class="h-12 py-2 currency w-80"
                            :selected="old('currency_msrp') !== null ? old('currency_msrp') : ($item !== null && $item->currency !== null ? $item->currency->id : null)"
                            :options="($currencies->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())" />
                        <x-common.input.input type="text" name="price_msrp" class=""
                            value="{{ (old('price_msrp') !== null) ? (old('price_msrp')) : (($item != null) ? ($item->price) : '')}}"
                            placeholder="99.99"/>
                    </div>
                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        Product image
                    </x-slot>

                    <div class="drag-file flex items-center justify-center">
                        <span class="flex items-center">
                            <svg class="mr-3" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.125 13.125C14.5057 13.125 15.625 12.0057 15.625 10.625C15.625 9.24429 14.5057 8.125 13.125 8.125C11.7443 8.125 10.625 9.24429 10.625 10.625C10.625 12.0057 11.7443 13.125 13.125 13.125Z" fill="#D2D2D2"/>
                            <path d="M28.125 1.875H1.875C1.18437 1.875 0.625 2.43437 0.625 3.125V26.875C0.625 27.5656 1.18437 28.125 1.875 28.125H28.125C28.8156 28.125 29.375 27.5656 29.375 26.875V3.125C29.375 2.43437 28.8156 1.875 28.125 1.875ZM28.125 18.125L21.6919 12.6831C21.4038 12.3969 20.9881 12.47 20.7756 12.7188L14.5225 20.0137L16.875 23.5938L7.89062 16.3869C7.6425 16.1887 7.28313 16.2081 7.05813 16.4331L1.875 20.625V3.75C1.875 3.40438 2.155 3.125 2.5 3.125H27.5C27.845 3.125 28.125 3.40438 28.125 3.75V18.125Z" fill="#D2D2D2"/>
                            </svg>
                            <span id="image_name">Drag or click to upload</span>
                        </span>
                        <input type="file" name="product_image" id="product_image" class="absolute z-40 opacity-0" accept="image/*">
                    </div>
                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        ASIN
                    </x-slot>
                    <div class="flex items-center space-x-3">
                        <x-common.input.input name="asin" class="py-2" placeholder="B08FYVHB8Z" x-model="asin"
                            value="{{ old('asin') !== null ? old('asin') : ($item !== null && $item->asin !== null ? $item->asin->id : null) }}" />
                    </div>

                </x-form.input>

                <x-form.input class="input" x-show="asin!=''">
                    <div class="flex items-center justify-between px-4 py-10">
                        <div class="text-2xl font-bold text-black ">Pricing Info</div>
                        <div class="cbx-btn">
                            <input type="checkbox" id="price_tracking" name="price_tracking" value="1" x-model="priceTracking"
                            {{ old('price_tracking') !== null ? 'checked' : '' }}>
                            <label for="price_tracking"
                                   x-text="priceTracking ? 'Remove amazon price tracking' : 'Add amazon price tracking' ">Add amazon price tracking</label>
                        </div>
                    </div>
                </x-form.input>

                <div x-show="!priceTracking">
                    <x-form.input>
                        <x-slot name="label">
                            Retailer
                        </x-slot>
                        <x-common.input.select name="agent_id" id="agent" class="w-full h-12 py-2"
                            x-model="retailer"
                            :options="($agents->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())" />
                    </x-form.input>


                    <x-form.input>
                        <x-slot name="label">
                            URL
                        </x-slot>
                        <x-common.input.input type="text" name="url" class="py-2"
                            value="{{ (old('url') !== null) ? (old('url')) : (($item != null) ? ($item->url) : '')}}"
                            placeholder="https://www.amazon.co.uk/" />
                    </x-form.input>

                    <x-form.input class="price-input">
                        <x-slot name="label">
                            Price
                        </x-slot>
                        <div class="flex items-center space-x-3">
                            <x-common.input.select name="currency_id" id="currency_id" class="h-12 py-2 currency w-80" default=""
                                :selected="old('currency_id') !== null ? old('currency_id') : ($item !== null && $item->currency_id !== null ? $item->currency_id->id : null)"
                                :options="($currencies->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())" />
                            <x-common.input.input type="text" name="original_msrp"
                                value="{{ (old('original_msrp') !== null) ? (old('original_msrp')) : (($item != null) ? ($item->original_msrp) : '')}}"
                                placeholder="99.99" class="py-2" />
                        </div>
                    </x-form.input>
                </div>

                <x-form.input>
                    <x-slot name="label">
                        Urgency
                    </x-slot>
                    <div class="flex items-center space-x-3">
                        <x-common.input.select name="urgency" id="urgency" class="h-12 py-2" default=""
                                :selected="old('urgency') !== null ? old('urgency') : null"
                                :options="($urgency_options->map(function($item) {
                            return (object)['key' => $item['key'], 'value' => $item['value']];
                        })->toArray())" />
                    </div>
                </x-form.input>

                <x-form.input>
                    <x-slot name="label">
                        Notes
                    </x-slot>
                   <textarea class="block w-full px-2 px-4 py-3 border rounded-md resize-none"
                   name="notes"
                   rows="5"
                   value="{{ (old('notes') !== null) ? (old('notes')) : (($item != null) ? ($item->notes) : '')}}"
                   placeholder="I need the extra products added for different varients of this device..."></textarea>
                </x-form.input>

                <x-common.button.button class="flex items-center justify-between py-4 mx-4 text-xl px-9" x-data="{}"
                    @click.prevent="$dispatch('submit-save-form')">
                    Submit
                    <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 12.355L5.34333 7L0 1.645L1.645 0L8.645 7L1.645 14L0 12.355Z" fill="white"/>
                    </svg>
                </x-common.button.button>

            </x-form.container>

        </form>

        <div class="py-10 text-lg font-bold text-gray-300 border-b border-gray-200 hover:text-gray-400 ">
            <a href="/admin/products" class="flex items-center">
            <svg class="mr-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="16" height="16" transform="translate(0 16) rotate(-90)" fill="white" />
                <path
                    d="M10.2733 5.10663L7.21998 8.16663L10.2733 11.2266L9.33332 12.1666L5.33332 8.16663L9.33332 4.16663L10.2733 5.10663Z"
                    fill="#00D49F" />
            </svg>
            go to database home
            </a>
        </div>

        <div class="flex items-center justify-center py-10 space-x-10 text-lg font-bold text-gray-300">
            <a class="hover:text-gray-400" href="/admin/products">Home</a>
            <a class="hover:text-gray-400" href="/admin/product_request">Product Request Form</a>
            <a class="hover:text-gray-400" href="#">Sound charts</a>
            <a class="hover:text-gray-400" href="/admin/deals">Deals dashboard</a>
            <a class="hover:text-gray-400" href="">Logout</a>
        </div>

        <div class="mt-10 text-xs font-semibold text-center text-gray-300 copyright">&copy; @php echo date('Y'); @endphp Authority Media. All rights
            reserved.</div>

    </div>

    @once
        @push('footerScripts')
            <script>
                document.querySelector("#product_image").onchange = function(e) {
                    document.querySelector("#image_name").innerText = 'File selected: '+this.value.replace(/^.*[\\\/]/, '');
                }
            </script>
        @endpush
    @endonce


</x-custom-full-layout>
