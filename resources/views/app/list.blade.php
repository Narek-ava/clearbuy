<x-custom-layout>

    <x-slot name="title">
        Apps
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/app?backUrl={{ urlencode($backUrl) }}">New App
                </x-common.button.a>
                <x-common.button.button x-data="" @click="$dispatch('open-import-popup')" type="alt-lite">
                    Import Options
                </x-common.button.button>
            </x-common.button.group>
            <x-common.a.a x-data="{show: false}" @click.prevent="$dispatch('show-delete-items-modal');"
                class="ml-6 text-red-500 hover:text-red-400" href="#">Delete Selected</x-common.a.a>
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
        <x-slot name="search">
            <div class="space-x-2">
                <label>Name</label>
                <input type="text" name="name" value="{{ Request()->name }}" class="border px-2 py-0.5">
            </div>
            <div class="space-x-2">
                <label>Type</label>
                <select class="border px-2 py-0.5" name="type">
                    <option value="any" {{ Request()->type == 'any' ? 'selected' : '' }}>any</option>
                    @foreach ($types as $type_id => $type_name)
                        <option value="{{ $type_id }}" {{ Request()->type === (string)$type_id ? 'selected' : '' }}>{{ $type_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-x-2">
                <label>Price</label>
                <span>from </span>
                <input type="number" name="price_from" value="{{ Request()->price_from }}" class="border px-2 py-0.5">
                <span>to </span>
                <input type="number" name="price_to" value="{{ Request()->price_to }}" class="border px-2 py-0.5">
            </div>
            <div class="space-x-2">
                <label>Brand</label>
                <input type="text" name="brand" value="{{ Request()->brand }}" class="border px-2 py-0.5">
            </div>
        </x-slot>

        @livewire('importable.apps-import', ['exportUrl'=> route('export-apps')])

        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
        <div class="overflow-auto">
            <form class="deleteItemsForm" action="delete_apps" method="post">
                @csrf
                <input type="hidden" name="backUrl" value="{{ $backUrl }}">
                <x-common.table.table x-data="tableComponent('{{$sort}}', '{{$order}}')">
                    <x-slot name="thead">
                        <x-common.table.th><x-common.input.checkbox @change="check" x-bind:checked="checked" /></x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="id" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="name" />
                        </x-common.table.th>
                        <x-common.table.th>logo</x-common.table.th>
                        <x-common.table.th>type</x-common.table.th>
                        {{-- <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="price" />
                        </x-common.table.th> --}}
                        <x-common.table.th>brand</x-common.table.th>
                        <x-common.table.th>os</x-common.table.th>
                        <x-common.table.th></x-common.table.th>
                    </x-slot>
                    @foreach ($items as $item)
                        <x-common.table.tr x-bind:class="{ 'border-b-2': !showSpoilers[{{ $item->id }}] }">
                            <x-common.table.td><x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" /></x-common.table.td>
                            <x-common.table.td>{{ $item->id }}</x-common.table.td>
                            <x-common.table.td>
                                <a href="#" class="flex items-center space-x-2 text-blue-900" @click.prevent="showSpoiler({{ $item->id }})">
                                    <span>{{ $item->name }}</span>
                                    <span x-show="!showSpoilers[{{ $item->id }}]">
                                        <x-common.arrow.sort-down class="border-blue-900" />
                                    </span>
                                    <span x-show="showSpoilers[{{ $item->id }}]" style="display: none;">
                                        <x-common.arrow.sort-up class="border-blue-900" />
                                    </span>
                                </a>
                            </x-common.table.td>
                            <x-common.table.td>
                                @if ($item->logo != null)
                                    <div class="w-16 h-16 bg-center bg-no-repeat bg-contain" style="background-image: url('{{ $item->logo }}');"></div>
                                @endif
                            </x-common.table.td>
                            <x-common.table.td>{{ $item->type->name }}</x-common.table.td>
                            {{-- <x-common.table.td>{{ $item->price }}</x-common.table.td> --}}
                            <x-common.table.td>{{ $item->brand !== null ? $item->brand->name : '' }}</x-common.table.td>
                            <x-common.table.td>
                                <x-common.badge.container>
                                    @foreach ($item->os as $os)
                                        <x-common.badge.badge class="text-white bg-gray-500">{{ $os->name }}</x-common.badge.badge>
                                    @endforeach
                                </x-common.badge.container>
                            </x-common.table.td>
                            <x-common.table.td>
                                <x-common.button.group  class="justify-end">
                                    <x-common.button.a href="/admin/app?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">
                                        Edit
                                    </x-common.button.a>
                                </x-common.button.group>
                            </x-common.table.td>
                        </x-common.table.tr>
                        <x-common.table.tr x-show="!!showSpoilers[{{ $item->id }}]">
                            <x-common.table.td colspan="7">
                                <x-common.h.h4>
                                    Countries
                                </x-common.h.h4>
                                <div class="p-2 mb-2">
                                    {{ $item->countries->map(function($item) {
                                        return $item->name;
                                    })->join(', ') }}
                                </div>
                                <x-common.h.h4>
                                    Images
                                </x-common.h.h4>
                                <div class="p-2 mb-2">
                                    <x-common.button.group>
                                        @foreach ($item->images as $image)
                                            <div class="w-16 h-16 bg-center bg-no-repeat bg-contain" style="background-image: url('{{ $image->url }}');"></div>
                                        @endforeach
                                    </x-common.button.group>
                                </div>
                                <x-common.h.h4>
                                    Links
                                </x-common.h.h4>
                                <div class="p-2 mb-2">
                                    <ul>
                                        @foreach ($item->links as $link)
                                            <li>
                                                <a href="{{ $link->url }}" target="_blank">
                                                    {{ $link->store->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <x-common.h.h4>
                                    Change log
                                </x-common.h.h4>
                                <div class="p-2 mb-2">
                                    <a href="{{ $item->change_log_url }}" target="_blank">
                                        {{ $item->change_log_url }}
                                    </a>
                                </div>
                            </x-common.table.td>
                        </x-common.table.tr>
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
