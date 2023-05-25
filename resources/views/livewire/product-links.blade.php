<div>
    <x-common.table.table>
        <x-slot name="thead">
            <x-common.table.th>Agent</x-common.table.th>
            <x-common.table.th>Description</x-common.table.th>
            <x-common.table.th>Old&nbsp;price</x-common.table.th>
            <x-common.table.th>New&nbsp;price</x-common.table.th>
            <x-common.table.th>Currency</x-common.table.th>
            <x-common.table.th>Link</x-common.table.th>
            <x-common.table.th>Is&nbsp;primary</x-common.table.th>
            <x-common.table.th></x-common.table.th>
        </x-slot>
        @foreach ($links as $link)
            @continue($link === null)
            <x-common.table.tr>
                <x-common.table.td class="align-top">
                    @livewire('agent-autocomplete', ['name' => 'links['.$loop->index.'][agent]', 'item' => $link->agent], key('agent_'.$loop->index))
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.textarea-limited limit="500" cols="40" name="links[{{ $loop->index }}][description]" value="{{ $link->description }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="number" min="0" name="links[{{ $loop->index }}][price_old]" value="{{ $link->price_old }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="number" min="0" name="links[{{ $loop->index }}][price_new]" value="{{ $link->price_new }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    @livewire('currency-autocomplete', ['name' => 'links['.$loop->index.'][currency]', 'item' => $link->currency], key('currency_'.$loop->index))
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.input.input type="text" name="links[{{ $loop->index }}][link]" value="{{ $link->link }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <input type="radio" wire:click="setPrimary({{ $loop->index }})" {{ $primaryLink == $loop->index ? 'checked' : '' }} name="primary_link" value="{{ $loop->index }}" />
                </x-common.table.td>
                <x-common.table.td class="align-top">
                    <x-common.a.a href="#" wire:click.prevent="remove({{ $loop->index }})" class="text-red-500">delete</x-common.a.a>
                </x-common.table.td>
            </x-common.table.tr>
        @endforeach
    </x-common.table.table>

    <x-common.button.group class="mt-2">
        <x-common.button.a href="#" wire:click.prevent="add">add</x-common.button.a>
    </x-common.button.group>
</div>
