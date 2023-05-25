@props(['name', 'value', 'limit'])

<div x-data="textareaLimited({{ $limit }}, `{{ str_replace("`", "'", $value) }}`)">
    <textarea

    {{ $attributes->merge(['class' => 'block border resize-none px-2 py-0.5 rounded-md w-full']) }}

    x-ref="textarea"
    x-model="value"
    x-on:keyup.debounce.500ms="autoresize($refs.textarea)"
    name="{{ $name }}"
    cols="50"></textarea>
    <span class="text-xs text-gray-600" x-text="getSymbolsLeftText()"></span>
</div>

@once
    @push('footerScripts')
        <script>
            function textareaLimited(limit, value = "") {
                return {
                    limit: limit,
                    value: value,
                    getSymbolsLeftText() {
                        this.value = this.value.substring(0, this.limit);
                        let symbolsLeft = this.limit - this.value.length;
                        if (symbolsLeft > 0) {
                            return symbolsLeft + " symbol" + (symbolsLeft == 1 ? "" : "s") + " left";
                        } else {
                            return "0 symbols left";
                        }
                    },
                    autoresize(element) {
                        element.style.height = "1px";
                        element.style.height = Math.max(52, (26 + element.scrollHeight)) + "px";
                    }
                }
            }
        </script>
    @endpush
@endonce
