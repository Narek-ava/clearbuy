<x-custom-layout>

    <x-slot name="title">
        Category
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Categories', 'list_path' => '/admin/categories', 'is_copy' => $is_copy, 'item'=> $item ])


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

    <form class="editItemForm font-grotesk" action="" method="post" x-data="{}"
        @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ isset($item) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-form.container class=" lg:w-8/12">
            <div class="flex mb-12 space-x-8">
                <x-form.input class="w-full">
                    <x-slot name="label">
                        Name *
                    </x-slot>
                    <x-common.input.input type="text" name="name"
                        value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '') }}" />
                </x-form.input>
                <x-form.input class="w-full">
                    <x-slot name="label">
                        Parent
                    </x-slot>
    {{--                <select x-data="categorySelect()" x-ref='categorySelect' x-on:change="categoryChanged($refs)"--}}
    {{--                    x-init="categoryChanged($refs)" class="border px-2 py-0.5" name="parent">--}}
    {{--                    <option value="0">-- select --</option>--}}
    {{--                    @foreach ($categories as $category)--}}
    {{--                    <option value="{{ $category->id }}" {{--}}
    {{--                                (old('parent') !== null) ?--}}
    {{--                                ((old('parent') == $category->id) ? 'selected' : '') :--}}
    {{--                                (--}}
    {{--                                    ($item !== null) ?--}}
    {{--                                    (($item->parent !== null && $item->parent->id == $category->id) ? 'selected' : '') :--}}
    {{--                                    ($parent == $category->id ? 'selected' : '')--}}
    {{--                                )--}}
    {{--                            }}>--}}
    {{--                        {{ $category->name }}--}}
    {{--                    </option>--}}
    {{--                    @endforeach--}}
    {{--                </select>--}}
                    <x-common.input.select
                        name="parent"
                        id="parent"
                        :required="false"
                        :selected="$errors->any() ? old('parent') : ($item !== null && $item->parent !== null ? $item->parent->id : null)"
                        :options="($categories->map(function($item) {
                            return (object)['key' => $item->id, 'value' => $item->name];
                        })->toArray())"
                    />
                    @once
                    @push('footerScripts')
                    <script>
                        function categorySelect() {
                                    return {
                                        categoryChanged($refs) {
                                            Livewire.emit('categoryChanged', $refs.categorySelect.value);
                                        }
                                    }
                                }
                    </script>
                    @endpush
                    @endonce
                </x-form.input>
            </div>


            

            <x-form.input>
                @livewire('category-attributes', [
                'name' => 'attribute_ids',
                'categoryId' => ($errors->any() ?
                    (old('parent')) :
                    ($item === null ? $parent :
                    ($item->parent !== null ? $item->parent->id : null))),
                'ownAttributes' => ($errors->any() ?
                    (old('attribute_ids') !== null ? old('attribute_ids') : []) :
                    ($item !== null ? $item->attributes->map(function($item) {
                        return $item->id;
                    })->toArray() : [])),
                'featuredAttributes' => ($errors->any() ?
                    (old('featured_attributes') !== null ? old('featured_attributes') : []) :
                    ($item !== null ? $item->featuredAttributes->map(function($item) {
                        return $item->id;
                    })->toArray() : []))
                ])
            </x-form.input>
        </x-form.container>
    </form>
</x-custom-layout>
