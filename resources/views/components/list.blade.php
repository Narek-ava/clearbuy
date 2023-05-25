@isset($search)
<div class="">
    <x-search>
        {{ $search }}
    </x-search>
</div>
@endisset

<div class="overflow-auto">
    {{ $slot }}
</div>
