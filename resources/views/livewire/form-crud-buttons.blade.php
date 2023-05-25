<x-slot name="top">
    <div class="flex items-center">
        <x-common.button.group>
            <x-common.button.a type="alt-white" href="{!! $list_path !!}">{{ $plural }}</x-common.button.a>
            <x-common.button.a href="#" x-data="{}" @click.prevent="$dispatch('submit-save-form')">Save changes</x-common.button.a>
            {{-- <x-common.button.button x-data="" @click="$dispatch('open-import-popup')" type="alt-lite">Import options</x-common.button.button> --}}

            @if ( !$is_copy  &&  $item !== null  )
                <x-common.input.input  name="item-id" class="sm-input" value="{{ $item->id }}" readonly="readonly" type="navbar-input" ></x-common.input.input>
                <x-common.a.a 
                    x-data="deleteItemsButton()" 
                    @click.prevent="$dispatch('delete-selected', { id: {{$item->id}}, action: 'delete_{{ $plural }}' });"
                    class="ml-6 text-red-500 hover:text-red-400" href="#">Delete item
                </x-common.a.a>

                @once
                    @push('footerScripts')
                        <script>
                            function deleteItemsButton() {
                                return {
                                    show: false
                                }
                            }

                            document.getElementsByName("item-id")[0].onclick = function() {
                                this.select();
                                document.execCommand("copy");
                            };
                        </script>
                    @endpush
                @endonce


            @endif
        </x-common.button.group>
    </div>
</x-slot>
