<x-custom-layout>

    <x-slot name="title">
        Attributes
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/attribute?backUrl={{ urlencode($backUrl) }}">New Attribute
                </x-common.button.a>
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
        {{-- <x-slot name="search">
            <div class="space-x-2">
                <label>Name</label>
                <input type="text" name="name" value="{{ Request()->name }}" class="border px-2 py-0.5">
            </div>
            <div class="space-x-2">
                <label>Group</label>
                <input type="text" name="group_name" value="{{ Request()->group_name }}" class="border px-2 py-0.5">
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
                <label>Kind</label>
                <select class="border px-2 py-0.5" name="kind">
                    <option value="any" {{ Request()->type == 'any' ? 'selected' : '' }}>any</option>
                    @foreach ($kinds as $kind_id => $kind_name)
                        <option value="{{ $kind_id }}" {{ Request()->kind === (string)$kind_id ? 'selected' : '' }}>{{ $kind_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-x-2">
                <label>Measures</label>
                <div class="grid grid-cols-2 gap-1 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($measures as $measure)
                        <label>
                            <input type="checkbox" {{ collect(Request()->measures)->contains($measure->id) ? 'checked' : '' }} name="measures[]" value="{{ $measure->id }}">
                            {{ $measure->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </x-slot> --}}

        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
        <form class="deleteItemsForm" action="delete_attributes" method="post">
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
                    <x-common.table.th>type</x-common.table.th>
                    <x-common.table.th>kind</x-common.table.th>
                    <x-common.table.th>group</x-common.table.th>
                    <x-common.table.th>measure</x-common.table.th>
                    <x-common.table.th>options</x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="sort_order">Sort order</x-common.sortable>
                    </x-common.table.th>
                    <x-common.table.th search="true" />
                </x-slot>
                @foreach ($items as $item)
                    <x-common.table.tr class="group">
                        <x-common.table.td><x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" /></x-common.table.td>
                        <x-common.table.td>{{ $item->id }}</x-common.table.td>
                        <x-common.table.td>{{ $item->name }}</x-common.table.td>
                        <x-common.table.td>{{ $types[$item->type] }}</x-common.table.td>
                        <x-common.table.td>{{ $item->kindName }}</x-common.table.td>
                        <x-common.table.td>{{ $item->group->name }}</x-common.table.td>
                        <x-common.table.td>{{ $item->measure ? $item->measure->name : '' }}</x-common.table.td>
                        <x-common.table.td>
                            <x-common.badge.container>
                                @foreach ($item->options as $option)
                                    <x-common.badge.badge class="text-white bg-gray-500">{{ $option->name }}</x-common.badge.badge>
                                @endforeach
                            </x-common.badge.container>
                        </x-common.table.td>
                        <x-common.table.td>{{ $item->sort_order }}</x-common.table.td>
                        <x-common.table.td>
                            <x-common.button.group  class="justify-end space-x-4">
                                <x-common.a.a href="attribute?copy_id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}"
                                    class="text-sm text-gray-400 group-hover:text-gray-500">
                                    Copy
                                </x-common.a.a>
                                <x-common.a.a href="#"
                                    @click.prevent="$dispatch('delete-selected', { id: {{$item->id}} })"
                                    class="text-sm text-red-400 group-hover:text-secondary">
                                    Delete
                                </x-common.a.a>
                                <x-common.button.a type="alt" href="/admin/attribute?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">
                                    Edit
                                </x-common.button.a>
                            </x-common.button.group>
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
        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}

    </x-list>

</x-custom-layout>
