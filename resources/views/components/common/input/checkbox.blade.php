@props(['disabled' => false])
<label class="checkbox__control">
	<input type="checkbox" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => '']) !!}>
	<div class="checkbox__control__indicator"></div>
</label>