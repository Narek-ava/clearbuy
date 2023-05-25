{{-- <div class="p-2 pr-0" x-data={show:false}>
    <div class="flex flex-row items-center justify-between">
        <x-common.button.group @click="show = !show"
            class="{{ $category->children->count() > 0 ? 'cursor-pointer text-blue-800' : '' }}">
<span>{{ $category->name }} </span>
@if ($category->children->count() > 0)
<x-common.arrow.sort-down x-show="!show" class="border-blue-800" />
<x-common.arrow.sort-up x-show="show" class="border-blue-800" style="display: none;" />
@endif
</x-common.button.group>
<x-common.button.group>
    <x-common.a.a href="#" class="text-red-500" @click="$dispatch('show-delete-category-modal', {{ $category->id }})">
        delete</x-common.a.a>
    <x-common.button.a href="/admin/category?id={{ $category->id }}&backUrl={{ urlencode($backUrl) }}">Edit
    </x-common.button.a>
    <x-common.button.a href="/admin/category?parent={{ $category->id }}&backUrl={{ urlencode($backUrl) }}">New child
    </x-common.button.a>
</x-common.button.group>
</div>
<div class="pt-2 border-b-2"></div>

@if ($category->children->count() > 0)
<div class="p-2 pr-0 border-l-2" style="display:none;" x-show="show">
    @foreach ($category->children as $child)
    <x-category.tree :category=$child backUrl="{{ $backUrl }}" />
    @endforeach
</div>
@endif
</div> --}}

@props(['category', 'backUrl'])

@php $c = $category->children->count() > 0 ? 'has-children' : '' @endphp
<tr {{ $attributes->merge(['class' => "border-gray-300 border-none $c "]) }}>
    <x-common.table.td class="relative w-5">
        <x-common.input.checkbox class="selectAllCheckable" name="items[]" value="{{ $category->id }}" />
    </x-common.table.td>
    <x-common.table.td class="border-b border-gray-300">{{ $category->name }} (ID {{ $category->id }})</x-common.table.td>
    <x-common.table.td class="border-b border-gray-300">
        <x-common.button.group class="justify-end">
            <x-common.a.a href="#" class="text-gray-400"
                @click="$dispatch('show-delete-category-modal', {{ $category->id }})">delete</x-common.a.a>
            @if($category->attributes()->count())
            <x-common.button.button x-data="" @click="$dispatch('open-import-popup', {{ $category->id }})" type="alt-lite">
                Import Options
            </x-common.button.button>
            @endif
            <x-common.button.a type="alt" class="uppercase"
                href="/admin/category?parent={{ $category->id }}&backUrl={{ urlencode($backUrl) }}">New child
            </x-common.button.a>
            <x-common.button.a type="alt" class="uppercase"
                href="/admin/category?id={{ $category->id }}&backUrl={{ urlencode($backUrl) }}">Edit</x-common.button.a>
        </x-common.button.group>
    </x-common.table.td>
</tr>
<tr class="{{ $category->children->count() > 0 ? 'child-row' : '' }}">
    <td colspan="3">

        @if ($category->children->count() > 0)
        <div class="pl-8 ">
            <table class="w-full child">
                @foreach ($category->children as $child)
                @php $c = $loop->last ? 'last-child' : '' @endphp
                <x-category.tree :category=$child backUrl="{{ $backUrl }}" class={{$c}} />
                @endforeach
            </table>
        </div>
        @endif
    </td>
</tr>
