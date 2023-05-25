<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if (isset($label))
        <label class="px-4 py-2 font-bold align-top">
            {{ $label }}
        </label>
        <div class="px-4 py-2 align-top">
            {{ $slot }}
        </div>
    @else
        <td colspan=2 class="px-4 py-2 align-top">
            {{ $slot }}
        </td>
    @endif
</div>
