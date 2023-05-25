<x-custom-layout>

    <x-slot name="title">
        Categories
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/category?backUrl={{ urlencode($backUrl) }}">New Category
                </x-common.button.a>
            </x-common.button.group>
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
    @livewire('importable.categories-import', ['exportUrl'=> route('export-categories')])
    <x-list>
        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
        <form class="deleteItemsForm" action="delete_categories" method="post">
            @csrf
            <input type="hidden" name="backUrl" value="{{ $backUrl }}">
            <x-common.table.table x-data="tableComponent()">
                <x-slot name="thead">
                    <x-common.table.th><x-common.input.checkbox @change="check" x-bind:checked="checked" /></x-common.table.th>
                    <x-common.table.th>
                        <x-common.sortable sort="{{ $sort }}" order="{{ $order }}" name="name" />
                    </x-common.table.th>
                    <x-common.table.th search="true"/>
                </x-slot>
                <tr>
                    @foreach ($items as $item)
                        <x-category.tree :category=$item backUrl="{{ $backUrl }}" />
                    @endforeach
                </tr>
            </x-common.table.table>
            <script>
                function tableComponent() {
                    return {
                        checked: false,
                        sort: '{{ $sort }}',
                        order: '{{ $order }}',
                        check($event) {
                            [...document.querySelectorAll("input.selectAllCheckable")].map((el) => {
                                el.checked = $event.target.checked;
                            });
                        },
                        applySort(targetSort) {
                            let url = new URL(window.location);
                            url.searchParams.delete('order');

                            if (targetSort != this.sort) {
                                url.searchParams.delete('sort');
                                url.searchParams.append('sort', targetSort);
                                url.searchParams.append('order', 'ASC');
                            } else {
                                if (this.order == 'ASC') {
                                    url.searchParams.append('order', 'DESC');
                                } else {
                                    url.searchParams.append('order', 'ASC');
                                }
                            }
                            window.location = url.href;
                        },
                        defaultSort() {
                            let url = new URL(window.location);
                            url.searchParams.delete('sort');
                            url.searchParams.delete('order');
                            window.location = url.href;
                        }
                    }
                }
            </script>
        </form>
        {{ $items->withQueryString()->links('vendor.pagination.custom-tailwind', ['allowedPerPages' => $allowedPerPages]) }}
    </x-list>

    @push('modals')
    <div style="display: none;" x-data="deleteCategoryModal()" x-show="show"
        x-on:show-delete-category-modal.window="showModal"
        class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemModal sm:py-28">
        <div @click.away="show = false" class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
            <span @click="show = false"
                class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10 ">
                <p class="text-lg font-semibold">Delete this category? </p>
                <x-common.button.group class="flex flex-row flex-no-wrap items-center justify-center mt-10 space-x-2">
                    <x-common.button.a type="alt" href="#"  @click.prevent="submitDelete">
                        Delete
                    </x-common.button.a>
                    <x-common.button.a href="#" @click.prevent="show = false">
                        Cancel
                    </x-common.button.a>
                </x-common.button.group>
            </div>
            <form class="deleteItemForm" style="display: none;" action="delete_categories" method="post">
                @csrf
                <input type="hidden" name="backUrl" value="{{ $backUrl }}">
                <input type="hidden" name="items[]" x-bind:value="category">
            </form>
        </div>
    </div>
    <script>
        function deleteCategoryModal() {
                return {
                    show: false,
                    category: 0,
                    submitDelete() {
                        document.querySelector('form.deleteItemForm').submit()
                    },
                    showModal($event) {
                        this.category = $event.detail;
                        this.show = true;
                    }
                }
            }
    </script>
    @endpush

</x-custom-layout>
