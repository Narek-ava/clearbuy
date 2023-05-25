<x-custom-layout>

    <x-slot name="title">
        Attribute groups
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/attribute_group?backUrl={{ urlencode($backUrl) }}">New Attribute group
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
        </x-slot> --}}

        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
        <form class="deleteItemsForm" action="delete_attribute_groups" method="post">
            @csrf
            <input type="hidden" name="backUrl" value="{{ $backUrl }}">
            <x-common.table.table x-data="tableComponent('{{$sort}}', '{{$order}}')">
                <x-slot name="thead">
                    <x-common.table.th>
                        <x-common.input.checkbox @change="check" x-bind:checked="checked" />
                    </x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="id" />
                    </x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="name" />
                    </x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="sort_order">Sort order</x-common.sortable>
                    </x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="repeatable">Repeatable</x-common.sortable>
                    </x-common.table.th>
                    <x-common.table.th search="true"/>
                </x-slot>
                @foreach ($items as $item)
                    <x-common.table.tr class="group">
                        <x-common.table.td>
                            <x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" />
                        </x-common.table.td>
                        <x-common.table.td>{{ $item->id }}</x-common.table.td>
                        <x-common.table.td>{{ $item->name }}</x-common.table.td>
                        <x-common.table.td>{{ $item->sort_order }}</x-common.table.td>
                        <x-common.table.td>
                            {!! $item->repeatable ? '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.6624 12.0559L21.5624 10.9246C21.5187 10.8746 21.4499 10.8496 21.3874 10.8496C21.3187 10.8496 21.2562 10.8746 21.2124 10.9246L13.5874 18.6059L10.8124 15.8309C10.7624 15.7809 10.6999 15.7559 10.6374 15.7559C10.5749 15.7559 10.5124 15.7809 10.4624 15.8309L9.3499 16.9434C9.2499 17.0434 9.2499 17.1996 9.3499 17.2996L12.8499 20.7996C13.0749 21.0246 13.3499 21.1559 13.5812 21.1559C13.9124 21.1559 14.1999 20.9121 14.3062 20.8121H14.3124L22.6687 12.4121C22.7562 12.3059 22.7562 12.1496 22.6624 12.0559Z" fill="#00D49F"/>
                            </svg>
                            ' : '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.4">
                                <path d="M21.0264 16.9999L16.9983 16.9999H15.0007L10.9727 16.9999C10.4246 16.9999 9.97386 16.5491 9.97386 16.0011C9.97386 15.7271 10.0843 15.4752 10.2655 15.294C10.4467 15.1128 10.6986 15.0023 10.9726 15.0023L15.0007 15.0023H16.9983L21.0264 15.0023C21.5744 15.0023 22.0252 15.4531 22.0252 16.0011C22.0252 16.5491 21.5744 16.9999 21.0264 16.9999Z" fill="#101E2E"/>
                                </g>
                                </svg>
                            ' !!}
                        </x-common.table.td>
                        <x-common.table.td>
                            <x-common.button.group  class="justify-end space-x-4">
                                <x-common.a.a href="attribute_group?copy_id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}"
                                    class="text-sm text-gray-400 group-hover:text-gray-500">
                                    Copy
                                </x-common.a.a>
                                <x-common.a.a href="#"
                                    @click.prevent="$dispatch('delete-selected', { id: {{$item->id}} })"
                                    class="text-sm text-red-400 group-hover:text-secondary">
                                    Delete
                                </x-common.a.a>
                                <x-common.button.a type="alt" href="/admin/attribute_group?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">
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
