@php
    $tabs = collect($__laravel_slots)->filter(function($item, $key) {
        return $key != "__default";
    });

    if($tabs->count() === 0) return;
@endphp

@if ($attributes->has('hiddentab'))
    @php
        $hiddentab = $attributes->get('hiddentab');
    @endphp
@else
    @php
        $hiddentab = false;
    @endphp
@endif

<div class="overflow-x-auto overflow-y-visible tabs" x-data="{active: '{{ $tabs->keys()[0] }}', hidetab: '{{ $hiddentab }}' }">
    <ul class="flex flex-row flex-wrap items-end justify-center buttons productMenu">
        @foreach ($tabs as $key => $value)
            <div x-show="hidetab != '{{ $key }}'">
                <li class="text-bold inline-block rounded px-3"  x-bind:class="{
                    'text-white active': active == '{{ $key }}',
                    'hover:bg-gray-300': active != '{{ $key }}'
                    }">
                    <a class="block px-6 py-2" href="#" @click.prevent="active = '{{ $key }}'">{{ $key }}</a>
                </li>
            </div>
        @endforeach
    </ul>
    <div class="py-4 overflow-x-auto overflow-y-visible content">
        @foreach ($tabs as $key => $value)
            <div x-show="active == '{{ $key }}'" style="display: none;">
                {{ $value }}
            </div>
        @endforeach
    </div>
</div>
