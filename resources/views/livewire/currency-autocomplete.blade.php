<div class="">

    <div style="{{ $item !== null ? '' : 'display: none;' }}" class="inline-block border px-4 py-0.5 space-x-2 whitespace-no-wrap">
        @if ($item !== null)
        <div class="auto-single-select">
            <span>{{ $item->symbol }}</span>
            <a href="#" wire:click.prevent="dismiss">&times;</a>
            <input type="hidden" name="{{ $name }}" value="{{ $item->id }}">
        </div>
        @endif
    </div>

    <div class="relative" style="{{ $item !== null ? 'display: none;' : '' }}">
        <x-common.input.input type="text" wire:keyup.debounce.500ms="autocomplete" wire:model="search"/>
        @if ($suggestions->isNotEmpty())
            <ul class="absolute z-10 w-full custom-autocomplete top-full">
                @foreach ($suggestions as $item)
                    <li>
                        <a href="#" wire:click.prevent="add({{ $item->id }})" >{{ $item->name }} ({{ $item->symbol }})</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
