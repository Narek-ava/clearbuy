@props(['checked'])
<label class="switch">
    <input type="checkbox" {{ $attributes }} @if($checked=="true") {{'checked'}}  @endif>
    <span class="slider"></span>
</label>
