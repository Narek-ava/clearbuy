<x-custom-layout>

    <x-slot name="title">App</x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>

    @livewire('form-crud-buttons', ['plural' => 'Apps', 'list_path' => '/admin/apps', 'is_copy' => $is_copy, 'item'=> $item ])

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

    @livewire('importable.apps-import', ['exportUrl'=> route('export-apps')])


        <form class="overflow-x-auto overflow-y-visible editItemForm" action="" method="post" x-data="{}" @submit-save-form.window="document.querySelector('form.editItemForm').submit()">
            @csrf
            <input type="hidden" name="id" value="{{ (!$is_copy && $item !== null) ? $item->id : ''}}">
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
                                Type *
                            </x-slot>
                            <x-common.input.select
                                name="type"
                                id="type"
                                :required="true"
                                :selected="old('type') !== null ? old('type') : ($item !== null ? $item->type->id : null)"
                                :options="($types->map(function($item, $index) {
                                    return (object)['key' => $index, 'value' => $item];
                                })->toArray())"
                            />
                        </x-form.input>
                        
                        <x-form.input>
                            <x-slot name="label">
                                Change log URL
                            </x-slot>
                            <x-common.input.input type="text" name="change_log_url"
                            value="{{ (old('change_log_url') !== null) ? (old('change_log_url')) : (($item != null) ? ($item->change_log_url) : '') }}" />
                        </x-form.input>
                        <x-form.input>
                            <x-slot name="label">
                                App Walkthrough Video
                            </x-slot>
                            <x-common.input.input type="text" name="video_url"
                            value="{{ ($errors->any()) ? (old('video_url')) : (($item != null) ? ($item->video_url) : '') }}" />
                        </x-form.input>
                        <x-form.input>
                            <x-slot name="label">
                                App Description
                            </x-slot>
                            <x-common.input.textarea name="description"
                                value="{{ (old('description') !== null) ? (old('description')) : (($item != null) ? ($item->description) : '') }}"
                            />
                        </x-form.input>
                        <x-form.input>
                            <x-slot name="label">
                                Logo
                            </x-slot>
                            @livewire('item-images', [
                                'name' => 'logo',
                                'path' => 'apps',
                                'multiple' => false,
                                'images' => is_null($item) ? [] : (is_null($item->logo) ? [] : $item->logo)
                                ])

                        </x-form.input>

                    </x-form.container>
                </x-slot>

                <x-slot name="Images">
                    <x-form.container>
                        <x-form.input>
                            @livewire('item-images', [
                                'name' => 'images[]',
                                'path' => 'apps',
                                'multiple' => true,
                                'images' => ($errors->any() ?
                                                (old('images') ? old('images') : []) :
                                                ($item !== null && $item->images !== null ? $item->images->map(function($item) {
                                                    return $item->path;
                                                }) : []))])
                        </x-form.input>
                    </x-form.container>
                </x-slot>

                <x-slot name="Relations">
                    <x-form.container>
                        <x-form.input>
                            <x-slot name="label">
                                Brand *
                            </x-slot>
                            <x-common.input.select
                                name="brand"
                                id="brand"
                                :required="true"
                                :selected="$errors->any() ? old('brand') : ($item !== null && $item->brand !== null ? $item->brand->id : null)"
                                :options="($brands->map(function($item) {
                                    return (object)['key' => $item->id, 'value' => $item->name];
                                })->toArray())"
                            />
                        </x-form.input>
                        <x-form.input>
                            <x-slot name="label">
                                Countries
                            </x-slot>
                            @livewire('country-autocomplete-multiple', ['name' => 'countries[]', 'items' => ($errors->any() ? old('countries') : ($item !== null ? $item->countries : []))])
                        </x-form.input>
                        <x-form.input>
                            <x-slot name="label">
                                OS
                            </x-slot>
                            @livewire('os-autocomplete-multiple', [
                                'name' => 'os[]',
                                'items' => ($errors->any() ? old('os') : ($item !== null ? $item->os : []))])
                        </x-form.input>
                    </x-form.container>
                    <div class="h-40 spacer"></div>
                </x-slot>

                <x-slot name="Store Links">

                        @if ($item !== null)
                            @livewire('app-links', ['links' => $item->links->map(function($item) {
                                return [
                                    'store_id' => $item->store_id,
                                    'free' => $item->free ?? false,
                                    'app_purchase' => $item->app_purchase ?? false,
                                    'price' => $item->price,
                                    'currency_id' => $item->currency_id,
                                    'url' => $item->url,
                                ];
                            })->toArray()])
                        @else
                            @livewire('app-links', ['links' => []])
                        @endif
                        <div class="h-40 spacer"></div>

                </x-slot>
            </x-common.tabs>
        </form>


</x-custom-layout>
