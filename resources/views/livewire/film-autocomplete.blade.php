<div class="">

    <div style="{{ $item !== null ? '' : 'display: none;' }}" class="inline-block border px-4 py-0.5 space-x-2 whitespace-no-wrap">
        @if ($item !== null)
            <span>{{ $item->name }}</span>
            <a href="#" wire:click.prevent="dismiss">&times;</a>
            <input type="hidden" name="{{ $name }}" value="{{ $item->id }}">
        @endif
    </div>

    <div class="relative" style="{{ $item !== null ? 'display: none;' : '' }}">
        <x-common.input.input type="text" wire:keyup.debounce.500ms="autocomplete" wire:model="search"/>
        @if ($suggestions->isNotEmpty())
            <ul class="absolute left-1 top-full border-b shadow-md z-10">
                @foreach ($suggestions as $item)
                    <li class="border-l border-r border-t">
                        <a href="#" wire:click.prevent="add({{ $item->id }})" class="bg-white block px-4 py-0.5 hover:bg-gray-200 focus:bg-gray-200">{{ $item->name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
