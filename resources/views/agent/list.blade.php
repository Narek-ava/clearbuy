<x-custom-layout>

    <x-slot name="title">
        Agents
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>


    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/agent?backUrl={{ urlencode($backUrl) }}">New Agent
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
                <label>Website</label>
                <input type="text" name="website" value="{{ Request()->website }}" class="border px-2 py-0.5">
            </div>
            <div class="space-x-2">
                <label>Is retailer</label>
                <select class="border px-2 py-0.5" name="is_retailer">
                    <option value="any">any</option>
                    <option value="1" {{ Request()->is_retailer == '1' ? 'selected' : '' }}>yes</option>
                    <option value="0" {{ Request()->is_retailer == '0' ? 'selected' : '' }}>no</option>
                </select>
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
        </x-slot>


        @livewire('importable.agents-import', ['exportUrl'=> route('export-agents')])

        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
        <form class="deleteItemsForm" action="delete_agents" method="post">
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
                    <x-common.table.th>website</x-common.table.th>
                    <x-common.table.th>type</x-common.table.th>
                    <x-common.table.th>is retailer</x-common.table.th>
                    <x-common.table.th></x-common.table.th>
                </x-slot>
                @foreach ($items as $item)
                    <x-common.table.tr>
                        <x-common.table.td><x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" /></x-common.table.td>
                        <x-common.table.td>{{ $item->id }}</x-common.table.td>
                        <x-common.table.td>{{ $item->name }} {{ $item->surname }}</x-common.table.td>
                        <x-common.table.td>
                            @if ($item->image != null)
                                <div class="w-16 h-16 bg-center bg-no-repeat bg-contain" style="background-image: url('{{ $item->image }}');"></div>
                            @endif
                        </x-common.table.td>
                        <x-common.table.td>
                            @if ($item->website)
                                <a href="{{ $item->website }}" target="_blank">
                                    {{ $item->website }}
                                </a>
                            @endif
                        </x-common.table.td>
                        <x-common.table.td>
                            {{ $item->type->name }}
                        </x-common.table.td>
                        <x-common.table.td>
                            {{ $item->is_retailer ? '+' : '-' }}
                        </x-common.table.td>
                        <x-common.table.td>
                            <x-common.button.group  class="justify-end">
                                <x-common.button.a href="/admin/agent?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">
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
