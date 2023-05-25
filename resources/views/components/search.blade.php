<div class="" x-data="searchForm()">
    <div class="cursor-pointer flex flex-row flex-no-wrap items-center justify-between bg-gray-900 text-white px-4 py-2" @click="open = open ^ true">
        <x-common.h.h4>Search</x-common.h.h4>
        <span class="text-sm"  x-show="open" style="display: none;">Hide</span>
        <span class="text-sm"  x-show="!open">Show</span>
    </div>
    <div style="display:none;" class="border-b-2 border-gray-300 px-4 py-2" x-show="open">
        <form class="space-y-2 searchForm" action="" method="get">
            <input type="hidden" name="perPage" value="{{ Request()->perPage }}">
            <input type="hidden" name="sort" value="{{ Request()->sort }}">
            <input type="hidden" name="order" value="{{ Request()->order }}">

            {{ $slot }}

            <x-common.button.group class="justify-end">
                <x-common.a.a href="#" @click.prevent="clearForm">Clear</x-common.a.a>
                <x-common.button.submit value="Submit" />
            </x-common.button.group>
        </form>
    </div>
</div>
<script>
    function searchForm() {
        return {
            open: false,
            clearForm() {
                let formData = new FormData(document.querySelector("form.searchForm"));
                let url = new URL(window.location.origin + window.location.pathname);
                let params = ["perPage", "sort", "order"];
                params.forEach((param) => {
                    if (formData.get(param)) {
                        url.searchParams.append(param, formData.get(param));
                    }
                });
                window.location = url;
            }
        }
    }
</script>
