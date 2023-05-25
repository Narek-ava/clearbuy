@php $class = 'border w-full rounded-md px-4 py-2 ' @endphp

@if(isset($type))

    @switch($type)
        @case('navbar-input')
            @php $class = 'bg-gray-200 text-gray-500 font-semibold rounded px-4 py-1 border ' @endphp
            @break
    @endswitch

@endif

<input {{ $attributes->merge(['class' => $class]) }} >
