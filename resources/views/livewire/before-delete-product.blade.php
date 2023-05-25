<div>

    <x-common.a.a x-data="deleteItemsButton()" @click="getAllChecked"
        class="ml-6 text-red-500 hover:text-red-400" href="#">Delete Selected</x-common.a.a>

    <div style="display: none;" x-data="deleteItemsModal()" x-show="show"
         x-on:show-delete-product-items-modal.window="showModal"
         class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemsModal sm:py-28">
        <div @click.away="show = false" class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
            <span @click="show = false" class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10">
                <p class="text-lg font-semibold pb-4">Are you sure you want to delete these products?</p>
                @if($namelist->isNotEmpty())
                  <ol class="text-left max-h-40 overflow-y-auto text-sm">
                    @foreach($namelist as $product_name)
                        <li>{{ $product_name }}</li>
                    @endforeach
                  </ol>
                @endif
                <x-common.button.group class="justify-between mt-10 space-x-4">
                    <x-common.button.a href="#" type="alt" @click.prevent="show = false" class="w-40" >
                        Cancel
                    </x-common.button.a>
                    <x-common.button.a href="#" @click.prevent="submitDelete" class="w-40">
                        Yes
                    </x-common.button.a>
                </x-common.button.group>
            </div>

        </div>
    </div>


    <div style="display: none;" x-data="deleteItemsModalHis()" x-show="show"
         x-on:show-confirm-history-modal.window="showModalHis"
         class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemsModal sm:py-28">
        <div @click.away="show = false"
             class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
                <span @click="show = false"
                      class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10 ">
                <p class="text-lg font-semibold">Some products have recorded price history. Would you still like to delete this products?</p>
                <x-common.button.group class="justify-between mt-10 space-x-4">
                    <x-common.button.a href="#" type="alt" @click.prevent="show = false" class="w-40">
                        Keep
                    </x-common.button.a>
                    <x-common.button.a href="#" @click.prevent="submitDeleteHis" class="w-40">
                        Yes
                    </x-common.button.a>
                </x-common.button.group>
            </div>

        </div>
    </div>

</div>

@once
    @push('footerScripts')
        <script>
            function deleteItemsButton() {
                return {
                    getAllChecked() {
                        var arIds = [];

                        [...document.querySelectorAll("input.selectAllCheckable")].map((el) => {
                            if(el.checked) arIds.push(el.value);
                        });

                        if(arIds.length) Livewire.emitTo('before-delete-product', 'dispatchPopup', arIds);
                    }
                }
            }

            function deleteItemsModalHis() {
                return {
                    show: false,
                    submitDeleteHis() {
                        document.querySelector('form.deleteItemsForm').submit()
                    },
                    showModalHis() {
                        if ([...document.querySelectorAll('form.deleteItemsForm input[type=checkbox][name]:checked')].length > 0) {
                            this.show = true;
                        }
                    }

                }
            }
        </script>
    @endpush
@endonce
