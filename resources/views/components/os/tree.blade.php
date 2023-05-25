@props(['item', 'backUrl', 'parent' => ''])

<x-common.table.tr x-data="{}">
    <x-common.table.td>{{ $item->id }}</x-common.table.td>
    <x-common.table.td>{{ $item->name }}</x-common.table.td>
    <x-common.table.td>{{ $parent }}</x-common.table.td>
    <x-common.table.td>
        @if ($item->image != null)
            <div class="w-16 h-16 bg-center bg-no-repeat bg-contain" style="background-image: url('{{ $item->imageUrl }}');"></div>
        @endif
    </x-common.table.td>
    <x-common.table.td>{{ $item->brand !== null ? $item->brand->name : '' }}</x-common.table.td>
    <x-common.table.td>{{ $item->licenseType !== null ? $item->licenseType->name : '' }}</x-common.table.td>
    <x-common.table.td>{{ $item->version }}</x-common.table.td>
    <x-common.table.td>{{ $item->is_kernel ? '+' : '-' }}</x-common.table.td>
    <x-common.table.td>
        <x-common.button.group  class="justify-end">
            <x-common.button.group>
                <x-common.a.a href="#" class="text-red-500"   @click.prevent="$dispatch('delete-selected', { id: {{$item->id}} })">delete</x-common.a.a>
                <x-common.button.a href="/admin/os?id={{ $item->id }}&backUrl={{ urlencode($backUrl) }}">Edit</x-common.button.a>
            </x-common.button.group>
        </x-common.button.group>
    </x-common.table.td>
</x-common.table.tr>
@if ($item->children->count() > 0)
    @foreach ($item->children as $child)
        <x-os.tree :item=$child backUrl="{{ $backUrl }}" :parent="mb_strlen($parent) > 0 ? $parent.' > '.$item->name : $item->name" />
    @endforeach
@endif
