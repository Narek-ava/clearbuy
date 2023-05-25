@props(['type' => '', 'href'=>''])
@php $class = 'font-bold text-white font-grotesk focus:outline-none bg-primary btn-shadow hover:bg-green-400 hover:shadow-none' @endphp

@if ($type!="")

    @switch($type)
        @case('alt')
            @php $class = 'border border-gray-400 focus:outline-none focus:ring focus:border-gray-300 font-grotesk hover:shadow-none btn-alt-shadow' @endphp
            @php $class .= ' text-gray-900 bg-gray-200 hover:bg-gray-300 font-bold' @endphp
            @break

        @case('alt-lite')
            @php $class = 'border border-gray-400 focus:outline-none focus:ring focus:border-gray-300 font-grotesk hover:shadow-none btn-alt-shadow' @endphp
            @php $class .= ' text-gray-500 font-semibold bg-gray-100 hover:bg-gray-200 ' @endphp
            @break

        @case('alt-white')
            @php $class = 'border border-gray-400 focus:outline-none focus:ring focus:border-gray-300 font-grotesk hover:shadow-none btn-alt-shadow' @endphp
            @php $class .= ' text-gray-500 font-semibold bg-white btn-alt-white-shadow hover:bg-gray-200' @endphp
            @break

        @case('alt-lite-full-width')
            @php $class = ' w-full add-more-btn  ' @endphp
            @break
    @endswitch

@endif
<button type="button" {{ $attributes->merge(['class' => $class.' py-1 px-5 rounded ']) }}>
    {{ $slot }}
</button>
