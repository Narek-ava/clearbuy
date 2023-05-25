<x-custom-layout>

    <x-slot name="title">
        Reviews
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/film_review?backUrl={{ urlencode($backUrl) }}">New Review
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
                <label>Title</label>
                <input type="text" name="title" value="{{ Request()->title }}" class="border px-2 py-0.5">
            </div>
            <div class="space-x-2">
                <label>Rating</label>
                <span>from </span>
                <input type="number" name="rating_from" value="{{ Request()->rating_from }}" class="border px-2 py-0.5">
                <span>to </span>
                <input type="number" name="rating_to" value="{{ Request()->rating_to }}" class="border px-2 py-0.5">
            </div>
            <div class="space-x-2">
                <label>Film</label>
                <input type="text" name="film" value="{{ Request()->film }}" class="border px-2 py-0.5">
            </div>
        </x-slot>

        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
        <div class="overflow-auto">
            <form class="deleteItemsForm" action="delete_film_reviews" method="post">
                @csrf
                <input type="hidden" name="backUrl" value="{{ $backUrl }}">
                <x-common.table.table x-data="tableComponent('{{$sort}}', '{{$order}}')">
                    <x-slot name="thead">
                        <x-common.table.th><x-common.input.checkbox @change="check" x-bind:checked="checked" /></x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="id" />
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="title" />
                        </x-common.table.th>
                        <x-common.table.th>
                            film
                        </x-common.table.th>
                        <x-common.table.th>
                            <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="rating" />
                        </x-common.table.th>
                        <x-common.table.th>
                            summary
                        </x-common.table.th>
                        <x-common.table.th>
                            recomendations
                        </x-common.table.th>
                        <x-common.table.th></x-common.table.th>
                    </x-slot>
                    @foreach ($items as $item)
                        <x-common.table.tr x-bind:class="{ 'border-b-2': !showSpoilers[{{ $item->id }}] }">
                            <x-common.table.td><x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $item->id }}" /></x-common.table.td>
                            <x-common.table.td>{{ $item->id }}</x-common.table.td>
                            <x-common.table.td>
                                {{ $item->title }}
                            </x-common.table.td>
                            <x-common.table.td>
                                {{ $item->film !== null ? $item->film->name : '' }}
                            </x-common.table.td>
                            <x-common.table.td>
                                {{ $item->rating }}
                            </x-common.table.td>
                            <x-common.table.td>
                                {{ $item->summary_short }}
                            </x-common.table.td>
                            <x-common.table.td>
                                {{ $item->recomendations->map(function($item) {
                                    return $item->name;
                                })->join(', ') }}
                            </x-common.table.td>
                            <x-common.table.td>
                                <x-common.button.group  class="justify-end">
                                    <x-common.button.a href="/admin/film_review?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">
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
        </div>
        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}

    </x-list>

</x-custom-layout>
