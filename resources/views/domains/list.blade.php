<x-custom-layout>

    <x-slot name="title">
        Allowed domains
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/domain?backUrl={{ urlencode($backUrl) }}">New Domain
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
        <x-slot name="search">
            <div class="space-x-2">
                <label>Domain</label>
                <input type="text" name="domain" value="{{ Request()->domain }}" class="border px-2 py-0.5">
            </div>
        </x-slot>


        <form class="deleteItemsForm" action="delete_domains" method="post">
            @csrf
            <input type="hidden" name="backUrl" value="{{ $backUrl }}">
            <x-common.table.table x-data="tableComponent('{{$sort}}', '{{$order}}')">
                <x-slot name="thead">
                    <x-common.table.th><x-common.input.checkbox @change="check" x-bind:checked="checked" /></x-common.table.th>
                    <x-common.table.th>ID</x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="domain" />
                    </x-common.table.th>
                    <x-common.table.th></x-common.table.th>
                </x-slot>
                @foreach ($items as $item)
                    <x-common.table.tr>
                        <x-common.table.td><x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" /></x-common.table.td>
                        <x-common.table.td>{{ $item->id }}</x-common.table.td>
                        <x-common.table.td>{{ $item->domain }}</x-common.table.td>
                        <x-common.table.td>
                            <x-common.button.group  class="justify-end">
                                <x-common.button.a href="/admin/domain?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">
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

    </x-list>


</x-custom-layout>
