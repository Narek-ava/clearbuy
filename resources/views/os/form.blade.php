<x-custom-layout>

    <x-slot name="title">
        OS
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'OSs', 'list_path' => '/admin/oss', 'is_copy' => $is_copy, 'item'=> $item ])

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

    <form class="overflow-x-auto overflow-y-visible editItemForm" action="" method="post" x-data="{}" @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
        @csrf
        <input type="hidden" name="id" value="{{ ($item !== null) ? $item->id : ''}}">
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-common.tabs>
            <x-slot name="General">
                <x-form.container>
                    <x-form.input>
                        <x-slot name="label">
                            Name *
                        </x-slot>
                        <x-common.input.input type="text" name="name"
                        value="{{ (old('name') !== null) ? (old('name')) : (($item != null) ? ($item->name) : '') }}" />
                    </x-form.input>
                    <x-form.input>
                        <x-slot name="label">
                            Description
                        </x-slot>
                        <x-common.input.textarea-limited name="description" limit="1000" value="{{ (old('description') !== null) ? (old('description')) : (($item != null) ? ($item->description) : '') }}"/>
                    </x-form.input>
                    <x-form.input>
                        <x-slot name="label">
                            Change log URL *
                        </x-slot>
                        <x-common.input.input type="text" name="change_log_url"
                        value="{{ (old('change_log_url') !== null) ? (old('change_log_url')) : (($item != null) ? ($item->change_log_url) : '') }}" />
                    </x-form.input>
                    <x-form.input>
                        <x-slot name="label">
                            Brand *
                        </x-slot>
                        <x-common.input.select
                            name="brand"
                            id="brand"
                            :required="true"
                            :selected="old('brand') !== null ? old('brand') : ($item !== null && $item->brand !== null ? $item->brand->id : null)"
                            :options="($brands->map(function($item) {
                                return (object)['key' => $item->id, 'value' => $item->name];
                            })->toArray())"
                        />
                    </x-form.input>
                    <x-form.input>
                        <x-slot name="label">
                            License *
                        </x-slot>
                        <x-common.input.select
                            name="license"
                            id="license"
                            :required="true"
                            :selected="old('license') !== null ? old('license') : ($item !== null && $item->licenseType !== null ? $item->licenseType->id : null)"
                            :options="($licenses->map(function($item) {
                                return (object)['key' => $item->id, 'value' => $item->name];
                            })->toArray())"
                        />
                    </x-form.input>
                    <x-form.input>
                        <label class="flex items-center space-x-1">
                            <span>Is kernel</span>
                            <div class="checkbox__control">
                                <input type="checkbox" name="is_kernel" value="1"
                                {{ (old('name') !== null) ? ((old('is_kernel')) ? 'checked' : '') : (($item !== null && $item->is_kernel) ? 'checked' : '') }}>
                                <div class="checkbox__control__indicator"></div>
                            </div>
                        </label>
                    </x-form.input>
                    <x-form.input>
                        <x-slot name="label">
                            Logo
                        </x-slot>
                        @livewire('item-images', [
                            'name' => 'image',
                            'path' => 'os',
                            'multiple' => false,
                            'images' => ($errors->any() ? (old('image') ? [old('image')] : []) : ($item !== null && $item->image !== null ? [$item->image] : []))])
                    </x-form.input>
                </x-form.container>
            </x-slot>

            <x-slot name="Releases">
                <div x-data='osReleases(
                    @json(
                        $errors->any() && old('releases') !== null ?
                        old('releases') :
                        ($item !== null ? $item->releases : [])
                    )
                )'>
                    <x-common.table.table>
                        <x-slot name="thead">
                            <x-common.table.th>Version *</x-common.table.th>
                            <x-common.table.th>Release date *</x-common.table.th>
                            <x-common.table.th>Added features</x-common.table.th>
                            <x-common.table.th></x-common.table.th>
                        </x-slot>
                        <template x-for="(item, index) in items" :key="index">
                            <x-common.table.tr>
                                <x-common.table.td class="align-top">
                                    <template x-if="items[index].id">
                                        <x-common.input.input type="hidden" x-bind:name="`releases[${index}][id]`" x-model="items[index].id" />
                                    </template>
                                    <x-common.input.input type="text" x-bind:name="`releases[${index}][version]`" x-model="items[index].version" />
                                </x-common.table.td>
                                <x-common.table.td class="align-top">
                                    <x-common.input.input type="date" x-bind:name="`releases[${index}][release_date]`" x-model="items[index].release_date" />
                                </x-common.table.td>
                                <x-common.table.td class="align-top">
                                    <ul class="ml-6 list-disc">
                                        <template x-for="(feature, f_index) in items[index].added_features" :key="f_index">
                                            <li class="px-2 py-0.5">
                                                <span x-text="feature"></span>
                                                <a href="#" x-on:click.prevent="removeFeature(index, f_index)">&times;</a>
                                                <input type="hidden" x-bind:name="`releases[${index}][added_features][]`" x-model="items[index].added_features[f_index]">
                                            </li>
                                        </template>
                                    </ul>
                                    <textarea
                                    class="block border resize-none px-2 py-0.5"
                                    :x-ref="'added_features_' + index"
                                    x-on:keydown.enter.prevent="addFeature(index, $refs)"
                                    placeholder="Press 'Enter' to add new list item"
                                    cols="50"></textarea>
                                </x-common.table.td>
                                <x-common.table.td class="align-top">
                                    <x-common.a.a href="#" class="text-red-500" x-on:click.prevent="remove(index)">remove</x-common.a.a>
                                </x-common.table.td>
                            </x-common.table.tr>
                        </template>
                    </x-common.table.table>
                    <x-common.button.group class="my-2">
                        <x-common.button.a href="#" x-on:click.prevent="add">Add</x-common.button.a>
                    </x-common.button.group>
                </div>
                <script>
                function osReleases(items) {
                    return {
                        items: items,
                        add() {
                            this.items.push({
                                added_features: []
                            });
                        },
                        addFeature(index, $ref) {
                            let input = $ref['added_features_' + index];
                            if (!input) return;
                            let val = input.value.trim();
                            if (val.length > 0) {
                                this.items[index].added_features.push(val);
                                input.value = "";
                            }
                        },
                        removeFeature(index, featureIndex) {
                            this.items[index].added_features.splice(featureIndex, 1);
                        },
                        remove(index) {
                            this.items.splice(index, 1);
                        }
                    }
                }
                </script>
            </x-slot>

            <x-slot name="Categories">
                <x-form.container>
                    <x-form.input>
                        <div class="flex flex-col">
                            @foreach ($categories as $category)

                            <label class="flex items-center space-x-1">
                                <div class="mr-1 checkbox__control">
                                    <input type="checkbox" {{ $errors->any() ?
                                        (old('categories') !== null && collect(old('categories'))->contains($category->id) ? 'checked' : '') :
                                        ($item !== null && $item->categories->map(function($item) {
                                            return $item->id;
                                        })->contains($category->id) ? 'checked' : '') }}
                                    name="categories[]" value="{{ $category->id }}">
                                    <div class="checkbox__control__indicator"></div>
                                </div>
                                {{ $category->name }}
                            </label>

                                
                            @endforeach
                        </div>
                    </x-form.input>
                </x-form.container>
            </x-slot>

            <x-slot name="Parent OS">
                <x-form.container>
                    <x-form.input>
                        <x-slot name="label">
                            Parent OS
                        </x-slot>
                        <x-common.input.select x-data="parentSelect()" x-ref='parentSelect' x-on:change="parentChanged($refs)" x-init="parentChanged($refs)"
                            name="parent"
                            id="parent"
                            :required="false"
                            :selected="old('parent') !== null ? old('parent') : ($item !== null && $item->parent !== null ? $item->parent->id : null)"
                            :options="($parents->map(function($item) {
                                return (object)['key' => $item->id, 'value' => $item->name];
                            })->toArray())"
                        />
                        @once
                            @push('footerScripts')
                                <script>
                                    function parentSelect() {
                                        return {
                                            parentChanged($refs) {
                                                Livewire.emit('parentChanged', $refs.parentSelect.value);
                                            }
                                        }
                                    }
                                </script>
                            @endpush
                        @endonce
                    </x-form.input>
                    <x-form.input>
                        <x-slot name="label">
                            Parent OS release
                        </x-slot>
                        @livewire('parent-os-release', [
                            'name' => 'parent_os_release',
                            'id' => 'parent_os_release',
                            'selected' => old('parent_os_release') !== null ? old('parent_os_release') : ($item !== null && $item->parentOSRelease !== null ? $item->parentOSRelease->id : null),
                            'parent' => old('parent') !== null ? old('parent') : ($item !== null && $item->parent !== null ? $item->parent->id : '')
                        ])
                    </x-form.input>
                </x-form.container>
            </x-slot>
        </x-common.tabs>
    </form>
</x-custom-layout>
