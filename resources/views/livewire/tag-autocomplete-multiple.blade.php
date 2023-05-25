<div>
    <div class="relative">
        <x-common.input.input type="text" wire:keyup.debounce.500ms="autocomplete" wire:keydown.enter="addRaw" wire:model="search"/>
        @if ($suggestions->isNotEmpty())
            <ul class="absolute z-10 w-full top-full custom-autocomplete">
                @foreach ($suggestions as $item)
                    <li>
                        <a href="#" wire:click.prevent="add({{ $item->id }})" >{{ $item->name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="flex flex-row items-center justify-start mt-1 space-x-2 tagContainer">
        @foreach ($items as $item)
            <div class="auto-single-select">
                <input type="hidden" name="{{ $name }}" value="{{ $item->id }}">
                <span>{{ $item->name }}</span>
                <a href="#" wire:click.prevent="remove({{ $item->id }})">&times;</a>
            </div>
        @endforeach
    </div>
</div>
