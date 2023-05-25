<div>

    <x-custom-layout>

        <x-slot name="title">
            Product
        </x-slot>

        <x-slot name="sidebarLinks">
            <x-sidebar-links :sidebarLinks=$sidebarLinks />
        </x-slot>

        <x-slot name="top" >
            <div class="flex items-center" x-data>

                <x-common.button.group>
                    <x-common.button.a href="/admin/products" type="alt-white" >Products</x-common.button.a>

                    <x-common.button.button @click="$dispatch('submit-save-form')">Save changes</x-common.button.button>

                    @if ( !$is_copy  &&  $item !== null  )
                        <x-common.input.input  name="item-id" class="sm-input" value="{{ $item->id }}" readonly="readonly" type="navbar-input" ></x-common.input.input>
                    @endif
                </x-common.button.group>

                <x-common.a.a href="#" @click.prevent="$dispatch('draft'); $dispatch('submit-save-form')" class="ml-6 text-black-500 hover:text-black-400" >Save draft</x-common.a.a>

                @if ( !$is_copy  &&  $item !== null  )
                    <x-common.a.a  @click.prevent="$dispatch('delete-selected', { id: {{$item->id}}, action: 'delete_products' });"
                    class="ml-6 text-red-500 hover:text-red-400" href="#">Delete
                          @if(is_null($item->parent_id)) product
                          @else variant
                          @endif
                    </x-common.a.a>

                    @once
                        @push('footerScripts')
                            <script>
                                document.getElementsByName("item-id")[0].onclick = function() {
                                    this.select();
                                    document.execCommand("copy");
                                };
                            </script>
                        @endpush
                    @endonce
                @endif

            </div>
        </x-slot>

        <form id="product_form" class="overflow-x-auto overflow-y-visible editItemForm" action="" method="post" x-data
            @submit-save-form.window="

                const form_data_obj = new FormData(product_form); //get all form items
                const form_values = Object.fromEntries(form_data_obj); //transform to object

                let ar_multiple_simple = ['similar','websites','badges','tags','images']; //like similar[]

                //transform multiple (e.g. similar[] to similar = [])
                ar_multiple_simple.forEach(function(item, i, arr) {
                    let item_brakets = item+'[]';
                    form_values[item] = form_data_obj.getAll(item_brakets);
                    delete form_values[item_brakets];
                });

                //transform multiple attributes (product_attributes_multiple[some_id][])
                let i = 0;
                var ar_pam = {};
                for(let [name, value] of form_data_obj) {
                    if(name.includes('product_attributes_multiple')) {
                        let attr_key = name.match(/multiple\[(.*)\]\[/);
                        ar_pam[i] = {'key': attr_key[1], 'val': value};
                        delete form_values[name];
                        i++;
                    }
                }
                form_values['product_attributes_multiple'] = ar_pam;
                @this.submit(form_values)">
            @csrf

            <input type="hidden" name="item_id" value="{{ (!$is_copy && $item !== null) ? $item->id : ''}}"  >

            <input type="hidden" name="backUrl" value="{{ $backUrl }}">
            <input type="hidden" name="draft" id="draft-input" value="0" @draft.window="document.getElementById('draft-input').value=1">
            <input type="hidden" name="next_variant" id="next-variant"  value="0">

            @livewire ('variant-multiple', [
                'product_id' => $item !== null ? $item->id : null,
                'parent_id'  => $item !== null ? $item->parent_id : null,
                'variant_id' => $item !== null ? $item->variant_id : null,
            ])

            <x-common.tabs>

                <x-slot name="Product info" >
                    <x-form.container>

                        <x-form.input>
                            <x-slot name="label">
                                Product name *
                            </x-slot>
                            <x-common.input.input type="text" name="name"  wire:model="item.name"
                                value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                SKU
                            </x-slot>
                            <x-common.input.input type="text" name="sku" wire:model="item.sku"
                                value="{{ (old('sku') !== null) ? (old('sku')) : (($item != null) ? ($item->sku) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                ASIN
                            </x-slot>
                            <x-common.input.input type="text" name="asin" wire:model="item.asin"
                                value="{{ (old('asin') !== null) ? (old('asin')) : (($item != null) ? ($item->asin) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Model number
                            </x-slot>
                            <x-common.input.input type="text" name="model" wire:model="item.model"
                                value="{{ (old('model') !== null) ? (old('model')) : (($item != null) ? ($item->model) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Model family
                            </x-slot>
                            <x-common.input.input type="text" name="model_family" wire:model="item.model_family"
                                value="{{ (old('model_family') !== null) ? (old('model_family')) : (($item != null) ? ($item->model_family) : '') }}" />
                        </x-form.input>

                        <x-form.input wire:ignore>
                            <x-slot name="label">
                                Category *
                            </x-slot>
                            <x-common.input.select
                                x-data="categorySelect({{ json_encode($is_first_render) }})"
                                x-ref="categorySelect"
                                x-on:change="categoryChanged($refs)" x-init="categoryChanged($refs)" name="category_id" id="category"
                                :required="true"
                                :selected="old('category') !== null ? old('category') : ($item !== null && $item->category !== null ? $item->category->id : null)"
                                :options="($categories->map(function($itm) {
                                    return (object)['key' => $itm->id, 'value' => $itm->name];
                                })->toArray())"/>

                                @once
                                    @push('footerScripts')
                                        <script>
                                            function categorySelect(is_first_render) {
                                                return {
                                                    is_first_render: is_first_render,
                                                    categoryChanged($refs) {
                                                        if(this.is_first_render) {
                                                            Livewire.emit('categoryChanged', $refs.categorySelect.value);
                                                        }
                                                    }
                                                }
                                            }
                                        </script>
                                    @endpush
                                @endonce

                        </x-form.input>

                        <x-form.input wire:ignore>
                            <x-slot name="label">
                                Brand *
                            </x-slot>
                            <x-common.input.select name="brand_id" id="brand" :required="true"
                                :selected="old('brand') !== null ? old('brand') : ($item !== null && $item->brand !== null ? $item->brand->id : null)"
                                :options="($brands->map(function($item) {
                                    return (object)['key' => $item->id, 'value' => $item->name];
                                })->toArray())" />
                        </x-form.input>

                        <x-form.input wire:ignore>
                            <x-slot name="label">
                                Retail Launch Price *
                            </x-slot>
                            <div class="flex items-center space-x-3">
                                <x-common.input.input type="text" name="price_msrp" wire:model="item.price_msrp"
                                    value="{{ (old('price_msrp') !== null) ? (old('price_msrp')) : (($item != null) ? ($item->price_msrp) : '')}}"
                                    class="flex-1"/>

                                <x-common.input.select name="currency_msrp" id="currency_msrp" wire:ignore
                                :required="true"
                                :selected="$errors->any() ? old('currency_msrp') : ($item !== null ? $item->currency_msrp : null)"
                                :options="($currencies->map(function($item) {
                                        return (object)['key' => $item->id, 'value' => $item->symbol];
                                    })->toArray())" />
                            </div>
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Released with OS
                            </x-slot>
                                @livewire('os-autocomplete', [
                                    'name' => 'released_with_os',
                                    'item' => (
                                            $errors->any() ? old('released_with_os') :
                                            ($item !== null && $item->releasedWithOS !== null ? $item->releasedWithOS->id : null)
                                        )
                                    ],
                                )
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Updatable to OS
                            </x-slot>
                            @livewire('os-autocomplete-multiple', [
                            'name' => 'updatable_to_os[]',
                            'items' => ($errors->any() ? old('updatable_to_os') : ($item !== null ? $item->updatableToOS :
                            []))])
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Length (mm)
                            </x-slot>
                            <x-common.input.input type="number" min="0" step="1" name="size_length" wire:model="item.size_length"
                                value="{{ (old('size_length') !== null) ? (old('size_length')) : (($item != null) ? ($item->size_length) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Width (mm)
                            </x-slot>
                            <x-common.input.input type="number" min="0" step="1" name="size_width" wire:model="item.size_width"
                                value="{{ (old('size_width') !== null) ? (old('size_width')) : (($item != null) ? ($item->size_width) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Height (mm)
                            </x-slot>
                            <x-common.input.input type="number" min="0" step="1" name="size_height" wire:model="item.size_height"
                                value="{{ (old('size_height') !== null) ? (old('size_height')) : (($item != null) ? ($item->size_height) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Weight (g)
                            </x-slot>
                            <x-common.input.input type="number" min="0" step="1" name="weight" wire:model="item.weight"
                                value="{{ (old('weight') !== null) ? (old('weight')) : (($item != null) ? ($item->weight) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                               Product page URL
                            </x-slot>
                            <x-common.input.input type="url" name="product_url" wire:model="item.product_url"
                                value="{{ (old('product_url') !== null) ? (old('product_url')) : (($item != null) ? ($item->product_url) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Product tagline
                            </x-slot>
                            <x-common.input.textarea-limited name="tagline" limit="50" wire:model.lazy="item.tagline"
                                value="{!! (old('tagline') !== null) ? (old('tagline')) : (($item != null) ? ($item->tagline) : '') !!}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Launch date *
                            </x-slot>
                            <x-common.input.input type="date" name="date_publish" wire:model="item.date_publish"
                                value="{{ (old('date_publish') !== null) ? (old('date_publish')) : (($item != null) ? ($item->date_publish) : '') }}" />
                        </x-form.input>
                    </x-form.container>
                </x-slot>

                <x-slot name="Editorial">
                    <x-form.container>
                        <x-form.input>
                            <x-slot name="label">
                                Overall rating
                            </x-slot>
                            <x-common.input.input type="number" min="0" max="10" step="1" name="rating" wire:model="item.rating"
                                value="{{ (old('rating') !== null) ? (old('rating')) : (($item != null) ? ($item->rating) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Review excerpt
                            </x-slot>
                            <x-common.input.textarea-limited name="excerpt" limit="100" wire:model.lazy="item.excerpt"
                                value="{!! (old('excerpt') !== null) ? (old('excerpt')) : (($item != null) ? ($item->excerpt) : '') !!}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Product summary
                            </x-slot>
                            <x-common.input.textarea name="summary_main"  wire:model.lazy="item.summary_main"
                                value="{!! (old('summary_main') !== null) ? (old('summary_main')) : (($item != null) ? ($item->summary_main) : '') !!}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Reasons to buy
                            </x-slot>
                            <x-common.input.textarea-limited name="reasons_to_buy" limit="500" wire:model.lazy="item.reasons_to_buy"
                                value="{{ (old('reasons_to_buy') !== null) ? (old('reasons_to_buy')) : (($item != null) ? ($item->reasons_to_buy) : '') }}"
                                placeholder="Reason 1 | Reason 2 | Reason 3" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Pros
                            </x-slot>
                            <x-common.input.textarea-limited name="pros" limit="500" wire:model.lazy="item.pros"
                                value="{{ (old('pros') !== null) ? (old('pros')) : (($item != null) ? ($item->pros) : '') }}"
                                placeholder="pros 1 | pros 2 | pros 3" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Cons
                            </x-slot>
                            <x-common.input.textarea-limited name="cons" limit="500" wire:model.lazy="item.cons"
                                value="{{ (old('cons') !== null) ? (old('cons')) : (($item != null) ? ($item->cons) : '') }}"
                                placeholder="cons 1 | cons 2 | cons 3" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Our take
                            </x-slot>
                            <x-common.input.textarea name="full_overview"
                                value="{!! (old('full_overview') !== null) ? (old('full_overview')) : (($item != null) ? ($item->full_overview) : '') !!}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Similar products
                            </x-slot>
                            @livewire('similar-products-autocomplete', [
                                'name' => 'similar[]',
                                'ownId' => ($item !== null && !$is_copy ? $item->id : 0),
                                'items' => ($errors->any() ? old('similar') : ($item !== null ? $item->similarProducts : []))
                            ])
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Websites
                            </x-slot>
                            @livewire('website-autocomplete-multiple', [
                                'name' => 'websites[]',
                                'items' => ($errors->any() ? old('websites') : ($item !== null ? $item->websites : []))
                            ])
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Review URL
                            </x-slot>
                            <x-common.input.input type="text" name="review_url" wire:model="item.review_url"
                                value="{{ (old('review_url') !== null) ? (old('review_url')) : (($item != null) ? ($item->review_url) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Buyers guide URL
                            </x-slot>
                            <x-common.input.input type="text" name="buyers_guide_url"
                                value="{{ (old('buyers_guide_url') !== null) ? (old('buyers_guide_url')) : (($item != null) ? ($item->buyers_guide_url) : '') }}" />
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Award badge(s)
                            </x-slot>
                            @livewire('badge-autocomplete-multiple', [
                                'name' => 'badges[]',
                                'items' => ($errors->any() ? old('badges') : ($item !== null ? $item->badges : []))
                            ])
                        </x-form.input>

                        <x-form.input>
                            <x-slot name="label">
                                Tags
                            </x-slot>
                            @livewire('tag-autocomplete-multiple', [
                                'name' => 'tags[]',
                                'items' => ($errors->any() ? old('$item->tags') : ($item !== null ? $item->tags : []))
                            ])
                        </x-form.input>
                    </x-form.container>
                </x-slot>

                <x-slot name="Images">
                    <x-form.container-row style="display: block">
                        <x-form.input>
                            @livewire('item-images', [
                            'name' => 'images[]',
                            'product_id' => $item->id,
                            'multiple' => true,
                            'path' => ($item !== null && $item->asin !== null) ? 'products/'.$item->asin : 'products',
                            'asin' => ($item !== null && $item->asin !== null) ? $item->asin : '',
                            'images' => ($errors->any() ?
                            (old('images') ? old('images') : []) :
                            ($item !== null && $item->images !== null ? $item->images->map(function($item) {
                            return $item->path;
                            }) : []))])
                        </x-form.input>
                    </x-form.container-row>
                </x-slot>

                @foreach ($attributeKinds as $kind_id => $kind_name)
                    <x-slot :name="$kind_name">
                        @livewire('product-attributes', [
                          'productId' => ($item !== null ? $item->id : null),
                          'old' => old('product_attributes'),
                          'kind_id' => $kind_id,
                          'is_first_render' => $is_first_render
                        ], key('product_attribute_'.$kind_id))
                    </x-slot>
                @endforeach

                <x-slot name="Ratings">
                    @include('ratings.ratings')
                </x-slot>

                <x-slot name="Content">
                    <div x-data='productContents(
                        @json(
                              $errors->any() ?
                              old('contents') : ($item !==null ? $item->contents : [])
                          )
                        )' wire:ignore>


                        <x-common.table.table>
                            <x-slot name="thead">
                                <x-common.table.th>Type *</x-common.table.th>
                                <x-common.table.th>Title *</x-common.table.th>
                                <x-common.table.th>Description</x-common.table.th>
                                <x-common.table.th>URL *</x-common.table.th>
                                <x-common.table.th></x-common.table.th>
                            </x-slot>
                            <template x-if="items != null" x-for="(item, index) in items" :key="item">
                                <x-common.table.tr>
                                    <x-common.table.td class="align-top">
                                        <x-common.input.select name="" x-bind:name="`contents[${index}][type_id]`"
                                            x-model="items[index].type_id" :required="true" :options="($contentTypes->map(function($item, $index) {
                                            return (object)['key' => $index, 'value' => $item];
                                        })->toArray())" />
                                    </x-common.table.td>
                                    <x-common.table.td class="align-top">
                                        <x-common.input.input type="text" x-bind:name="`contents[${index}][title]`"
                                            x-model="items[index].title" />
                                    </x-common.table.td>
                                    <x-common.table.td class="align-top">
                                        <textarea class="block border resize-none px-2 py-0.5"
                                            x-model="items[index].description"
                                            x-bind:name="`contents[${index}][description]`" cols="50"></textarea>
                                    </x-common.table.td>
                                    <x-common.table.td class="align-top">
                                        <x-common.input.input type="text" x-bind:name="`contents[${index}][url]`"
                                            x-model="items[index].url" />
                                    </x-common.table.td>
                                    <x-common.table.td class="align-top">
                                        <x-common.a.a href="#" class="text-red-500" x-on:click.prevent="remove(index)">
                                            remove</x-common.a.a>
                                    </x-common.table.td>
                                </x-common.table.tr>
                            </template>
                        </x-common.table.table>
                        <x-common.button.group class="my-2">
                            <x-common.button.a href="#" x-on:click.prevent="add">Add</x-common.button.a>
                        </x-common.button.group>
                    </div>

                    @once
                        @push('footerScripts')
                            <script>
                                function productContents(items) {
                                    return {
                                        items: items,
                                        add() {
                                            this.items.push({});
                                        },
                                        remove(index) {
                                            this.items.splice(index, 1);
                                        }
                                    }
                                }
                            </script>
                        @endpush
                    @endonce

                </x-slot>

                <x-slot name="Affliate & pricing">
                    <x-form.input>
                        <h3 class="mb-4 text-lg font-bold leading-5 ">Is promoted?</h3>
                        <label class="flex items-center space-x-1">
                            <x-common.input.switch name="is_promote" value="1"
                            :checked="(old('name') !== null) ? ((old('is_promote')) ? 'true' : 'false') : (($item !== null && $item->is_promote == true) ? 'true' : 'false')" />
                        </label>
                    </x-form.input>
                    <hr class="my-8">

                    <h3 class="mb-4 text-lg font-bold leading-5 ">Deal prices</h3>

                    <div class="w-full mb-2">
                        @livewire('deal-price-import')
                    </div>

                    @if ($item !== null || $errors->any())
                        @livewire('product-deal-prices', ['deal_prices' => $item->deal ?? [] ])
                    @endif
                    <hr class="my-8">
                    <h3 class="mb-4 text-lg font-bold leading-5 ">Product prices</h3>
                    <div class="mb-3 text-lg text-gray-400 ">Current and original price will be tracked automatically for
                        agent <strong>Amazon</strong>.</div>
                    @if ($item !== null || $errors->any())
                    @if($errors->any())
                    @php $product_prices = collect(old('product_prices')) @endphp
                    @else
                    @php $product_prices = collect($item->prices) @endphp
                    @endif

                    @livewire('product-prices', ['product_prices' => $product_prices->toArray(), 'asin'=>$item->asin ?? null])
                    @else
                    @livewire('product-prices', ['product_prices' => [] ])
                    @endif
                </x-slot>

                @if ($item != null)
                    <x-slot name="Price changes">
                        <x-common.table.table>
                            <x-slot name="thead">
                                <x-common.table.th>Date</x-common.table.th>
                                <x-common.table.th>Price type</x-common.table.th>
                                <x-common.table.th>Old price</x-common.table.th>
                                <x-common.table.th>New price</x-common.table.th>
                                <x-common.table.th>Reason</x-common.table.th>
                            </x-slot>
                            @foreach ($item->priceChanges as $change)
                            <x-common.table.tr>
                                <x-common.table.td>{{ $change->created_at }}</x-common.table.td>
                                <x-common.table.td>{{ $change->price_type }}</x-common.table.td>
                                <x-common.table.td>{{ $change->price_old }} {{ $change->oldCurrency->symbol ?? '' }}
                                </x-common.table.td>
                                <x-common.table.td>{{ $change->price_new }} {{ $change->newCurrency->symbol ?? '' }}
                                </x-common.table.td>
                                <x-common.table.td>{{ $change->reason }}</x-common.table.td>
                            </x-common.table.tr>
                            @endforeach
                        </x-common.table.table>
                    </x-slot>
                @endif

            </x-common.tabs>
        </form>

    </x-custom-layout>

    @if (session('status') == 'success')
        <x-common.alert.success>{{ session('message') }}</x-common.alert.success>
    @endif

    @if ($errors->any())
        <div class="space-y-1">
            @foreach ($errors->all() as $error)
                <x-common.alert.error>{{ $error }}</x-common.alert.error>
            @endforeach
        </div>
    @endif

    {{-- Delete attribute set --}}

    <div  x-data="deleteAttrSet()" x-show="show" x-cloak
         @delete-selected-attr-set.window="item = $event.detail.id; show=true"
         class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemModal sm:py-28">
        <div @click.away="show = false"
             class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
                <span @click="show = false"
                      class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10 ">
                <p class="text-lg font-semibold">Delete selected attributes set? </p>
                <x-common.button.group class="flex flex-row flex-no-wrap items-center justify-center mt-10 space-x-2">
                    <x-common.button.a href="#" type="alt" @click.prevent="show = false">
                        Cancel
                    </x-common.button.a>
                    <x-common.button.a href="#" @click.prevent="submitDelete">
                        Delete
                    </x-common.button.a>
                </x-common.button.group>
            </div>

        </div>
    </div>

</div>
