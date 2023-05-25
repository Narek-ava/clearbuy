<x-common.button.group>
    @if ($sort == $name)
        <a href="#" @click.prevent.once="defaultSort">
            &times;
        </a>
    @endif

    <a href="#"
    @click.prevent.once="applySort('{{ $name }}')"
    @if ($sort == $name)
        class="flex flex-row flex-no-wrap items-center space-x-1"
    @endif
    >
        <span>{{ strlen($slot) > 0 ? $slot : $name }}</span>
        @if ($sort == $name)
            @if ($order == 'ASC')
                <x-common.arrow.sort-down class="border-white" />
            @else
                <x-common.arrow.sort-up class="border-white" />
            @endif
        @endif
    </a>

</x-common.button.group>
