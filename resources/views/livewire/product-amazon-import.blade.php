<div>


    {{-- ASIN import popup --}}
    <div style="display:none;" x-data="{show:false}" x-show="show" @open-import-asin-popup.window="!isNaN($event.detail) ? $wire.category_id = $event.detail : ''; show=true "
        class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 importItemsModal sm:py-28">
        <div @click.away="show = false"
            class="relative mx-auto bg-white rounded popup-shadow w-96">
            <span @click="show = false"
                class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
            <div class="p-10 ">
                <p class="mb-6 text-lg font-semibold text-center">Import via ASIN</p>

                <div class="flex flex-col">
                    <x-form.input>
                        <x-slot name="label">
                            ASIN
                        </x-slot>
                        <x-common.input.input wire:model="asin" size="40" type="text" name="asin"
                            value="{{ old('asin') }}" placeholder="B08BX7N9SK" />
                        @error('asin') <div class="py-2 font-bold text-opacity-75 text-secondary">{{ $message }}</div> @enderror
                    </x-form.input>
                    <x-common.button.group
                        class="flex flex-row flex-no-wrap items-center justify-center space-x-2">

                        <x-common.button.button wire:click="getItemInfo">
                            <svg wire:loading wire:target="getItemInfo" class="w-5 h-5 mr-4 ml-4 text-gray-100 animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="getItemInfo">Import</span>
                        </x-common.button.button>
                    </x-common.button.group>

                    <div class="py-2 font-bold text-opacity-75 text-secondary">{{ $notifyer }}</div>

                </div>
            </div>

        </div>
    </div>

</div>
