@props(['name', 'items' => []])

<div x-data='tagInput("{{ $name }}", @json($items))'>
    <x-common.input.input type="text" x-on:keydown.enter="add" />
    <div class="tagContainer flex flex-row justify-start items-center space-x-2 mt-1" x-html="renderItems()"></div>
</div>
@once
    @push('footerScripts')
        <script>
            function tagInput(name, items = []) {
                return {
                    items: items,
                    name: name,
                    renderItems() {
                        return this.items.reduce((html, item) => {
                            return html + `<div class="input-tag">
                                        <input type="hidden" name="${this.name}" value="${item}">
                                        <span>${item}</span>
                                        <a href="#" x-on:click.prevent="remove('${item}')">&times;</a>
                                    </div>`;
                        }, "");
                    },
                    add($event) {
                        let val = event.target.value.trim();

                        if (val.length == 0) {
                            return;
                        }

                        if (this.items.includes(val)) {
                            return;
                        }

                        this.items.push(val);

                        event.target.value = "";
                    },
                    remove(item) {
                        this.items.splice(this.items.indexOf(item), 1);
                    }
                }
            }
        </script>
    @endpush
@endonce
