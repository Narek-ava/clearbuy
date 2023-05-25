<x-custom-layout>

    <x-slot name="title">
        OS
    </x-slot>

    <x-slot name="sidebarLinks">
        <x-sidebar-links :sidebarLinks=$sidebarLinks />
    </x-slot>
    <x-slot name="top">
        <div class="flex items-center">
            <x-common.button.group>
                <x-common.button.a href="/admin/os?backUrl={{ urlencode($backUrl) }}">New OS
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
    <form class="deleteItemsForm" action="delete_oss" method="post">
        @csrf
        <input type="hidden" name="backUrl" value="{{ $backUrl }}">
        <x-common.table.table>
            <x-slot name="thead">
                <x-common.table.th>ID</x-common.table.th>
                <x-common.table.th>name</x-common.table.th>
                <x-common.table.th>parent</x-common.table.th>
                <x-common.table.th>Logo</x-common.table.th>
                <x-common.table.th>brand</x-common.table.th>
                <x-common.table.th>license</x-common.table.th>
                <x-common.table.th>version</x-common.table.th>
                <x-common.table.th>Is&nbsp;kernel</x-common.table.th>
                <x-common.table.th></x-common.table.th>
            </x-slot>
            @foreach ($items as $item)
                <x-os.tree :item=$item backUrl="{{ $backUrl }}" />
            @endforeach
        </x-common.table.table>
    </form>


</x-custom-layout>
