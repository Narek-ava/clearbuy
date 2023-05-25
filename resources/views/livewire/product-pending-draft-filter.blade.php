
<select class="border bg-white js-choice rounded-md px-2 py-1 {{ $class }}" wire:model="selected" id="prod_pending_filter">
    @if ($default)
        <option {{ $selected === null ? 'selected' : '' }} value="">{{ $default }}</option>
    @endif
    @foreach ($options as $option)
        <option value="{{ $option->key }}" {{ $selected !== null && $selected == $option->key ? 'selected' : '' }}>
            {{ $option->value }}
        </option>
    @endforeach
</select>

@once
    @push('footerScripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                Livewire.hook('element.updated', (el, component) => {
                    if(el.id == 'prod_pending_filter') window.location.reload();
                });
            });
        </script>
    @endpush
@endonce
