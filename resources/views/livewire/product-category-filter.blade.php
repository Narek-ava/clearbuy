
<select class="border bg-white js-choice rounded-md px-2 py-1 {{ $class }}" wire:model="selected" id="prod_cat_filter">
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
                    if(el.id == 'prod_cat_filter') window.location.reload();
                });
            });

            // Pass single element
            // element = document.querySelector('#prod_cat_filter');
            // var pcat = new Choices(element, {
            //     classNames: {
            //         containerOuter: 'choices mb-0 flex-1',
            //         containerInner: 'bg-white border rounded p-0',
            //         listSingle: 'choices__list--single px-4 py-2',
            //         itemChoice: 'choices__item--choice bg-gray-200 bg-opacity-25 m-1 rounded',
            //     }
            // });

        </script>
    @endpush
@endonce
