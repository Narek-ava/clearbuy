@props(['name', 'value'])

<div x-data="textarea()" x-init="autoresize($refs.textarea)">

    <textarea {{ $attributes->merge(['class' => 'block border resize-none px-2 py-3 rounded-md w-full']) }} x-ref="textarea" x-on:keyup.debounce.500ms="autoresize($refs.textarea)" name="{{ $name }}" cols="50">{{ trim($value) }}</textarea>

</div>

@once
    @push('footerScripts')
        <script>
            function textarea() {
                return {
                    autoresize(element) {
                        element.style.height = "1px";
                        element.style.height = Math.max(52, (28 + element.scrollHeight)) + "px";
                    }
                }
            }
        </script>
    @endpush
@endonce
