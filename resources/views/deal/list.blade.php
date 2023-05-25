<x-custom-layout>

    <x-slot name="title">
        Deals
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/deal?backUrl={{ urlencode($backUrl) }}">New Deal
                </x-common.button.a>
                <x-common.button.button x-data="" @click="$dispatch('open-import-popup')" type="alt-lite">
                    Import Options
                </x-common.button.button>
            </x-common.button.group>
            <x-common.a.a x-data="{show: false}" @click.prevent="$dispatch('show-delete-items-modal');"
                class="ml-6 text-red-300 hover:text-red-500" href="#">Delete Selected</x-common.a.a>
        </div>
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


    <x-list>


        <div class="float-left my-3 mr-3">
            @livewire('toggle-session-store', ['inactive' => 'All', 'active'=>'Active' ])
        </div>

        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}

        <div>
            <form class="deleteItemsForm" action="delete_deals" method="post">
                @csrf
                <input type="hidden" name="backUrl" value="{{ $backUrl }}">
                <x-common.table.table x-data="tableComponent('{{$sort}}', '{{$order}}')" id="deals_table">
                    <x-slot name="thead">
                        <x-common.table.th>
                            <x-common.input.checkbox @change="check" x-bind:checked="checked" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="id" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="product" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="agent" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="price" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="created_at">Published date
                            </x-common.sortable>
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="expiry_date">Expiry date
                            </x-common.sortable>
                        </x-common.table.th>
                        <x-common.table.th>
                            URL
                        </x-common.table.th>
                        <x-common.table.th search="true"/>
                    </x-slot>
                    @foreach ($items as $item)
                    @if($item->product)
                    <x-common.table.tr class="group">
                        <x-common.table.td>
                            <x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" />
                        </x-common.table.td>
                        <x-common.table.td>{{ $item->id }}</x-common.table.td>
                        <x-common.table.td>
                            <a href="/admin/product?id={{ $item->product->id }}" class="flex items-center space-y-1 font-bold text-black" target="_blank">
                                <div>
                                    <div>{{ $item->product->name }} ({{ $item->product->variant->name ?? 'Default' }})</div>
                                    <div class="text-xs tracking-widest text-red-500 uppercase">
                                        {{ !$item->getCarbonDate($item->expiry_date)->isFuture() ? 'Expired' : '' }}</div>
                                </div>
                            </a>
                        </x-common.table.td>
                        <x-common.table.td>{{ $item->agent->name }}</x-common.table.td>
                        <x-common.table.td>{{ $item->currency->symbol.''.$item->price }}</x-common.table.td>
                        <x-common.table.td class="whitespace-no-wrap">{{ $item->created_at }}</x-common.table.td>
                        <x-common.table.td class="whitespace-no-wrap">{{ $item->getCarbonDate($item->expiry_date)->format('m/d/Y')  }}
                        </x-common.table.td>
                        <x-common.table.td>
                            @if ($item->url !="")
                            <a href="{{ $item->url }}" title="{{ $item->url }}">
                                @if (strlen($item->url) > 30)
                                {{ substr($item->url,0 ,30) }}...
                                @else
                                {{$item->url}}
                                @endif
                            </a>
                            @endif
                        </x-common.table.td>
                        <x-common.table.td>
                            <x-common.button.group class="justify-end space-x-4">
                                <x-common.a.a href="deal?copy_id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}"
                                    class="text-sm text-gray-400 group-hover:text-gray-500">
                                    Copy
                                </x-common.a.a>
                                <x-common.a.a href="#"
                                    @click.prevent="$dispatch('delete-selected', { id: {{$item->id}} })"
                                    class="text-sm text-red-400 group-hover:text-secondary">
                                    Delete
                                </x-common.a.a>
                                <x-common.button.a
                                    href="/admin/deal?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}" type="alt">
                                    Edit
                                </x-common.button.a>
                            </x-common.button.group>
                        </x-common.table.td>
                    </x-common.table.tr>
                    @endif
                    @endforeach
                </x-common.table.table>
                @once
                    @push('footerScripts')
                        <script src="/js/tableComponent.js"></script>
                    @endpush
                @endonce
            </form>
        </div>
        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}

    </x-list>

</x-custom-layout>
