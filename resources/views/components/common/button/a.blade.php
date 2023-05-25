@props(['type' => '', 'href'=>''])   
@php $class = 'font-semibold text-white font-grotesk bg-primary btn-shadow hover:bg-green-400 hover:shadow-none' @endphp

@if ($type!="")
    @php $class = 'text-sm text-gray-500 border border-gray-400 font-grotesk hover:shadow-none btn-alt-shadow' @endphp
    @if ($type=="alt")
        @php $class .= '  bg-gray-100 hover:bg-gray-200 font-bold' @endphp
    @elseif($type=="alt-lite")
        @php $class .= ' font-semibold bg-gray-100 hover:bg-gray-200 ' @endphp
    @elseif($type=="alt-white")
        @php $class .= ' font-semibold bg-white btn-alt-white-shadow hover:bg-gray-200' @endphp
    @endif    
@endif
<a href="{{ $href ?? '' }}" {{ $attributes->merge(['class' => $class.' py-1 px-5 rounded ']) }}>
    {{ $slot }}
</a>
