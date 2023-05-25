<x-custom-full-layout>

    @push('scripts')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime&display=swap" rel="stylesheet">
    @endpush

    <x-slot name="title">
        Product request Successfull
    </x-slot>

    @if (session('status') == 'success')
    <x-common.alert.success>
        {{ session('message') }}
    </x-common.alert.success>
    @endif
    <div class="mx-auto text-center text-gray-500 lg:w-5/12 font-grotesk product-request-success">
        <div class="pb-20 border-b border-gray-200">
            <div>
                <h3 class="flex items-center justify-center pt-10 pb-6 text-3xl heading">
                    <svg class="mr-6" width="28" height="22" viewBox="0 0 28 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M27.3253 3.11172L25.1253 0.849218C25.0378 0.749218 24.9003 0.699219 24.7753 0.699219C24.6378 0.699219 24.5128 0.749218 24.4253 0.849218L9.17529 16.2117L3.62529 10.6617C3.52529 10.5617 3.40029 10.5117 3.27529 10.5117C3.15029 10.5117 3.02529 10.5617 2.92529 10.6617L0.700293 12.8867C0.500293 13.0867 0.500293 13.3992 0.700293 13.5992L7.70029 20.5992C8.15029 21.0492 8.70029 21.3117 9.16279 21.3117C9.82529 21.3117 10.4003 20.8242 10.6128 20.6242H10.6253L27.3378 3.82422C27.5128 3.61172 27.5128 3.29922 27.3253 3.11172Z"
                            fill="#00D49F" />
                    </svg>

                    Your product request has been submitted.
                </h3>
                @if($product->urgency != null)
                    <div class="sub-heading">The full product details will be added within <span class="text-primary">{{ $product->urgency->diffForHumans() }}</span>. </div>
                @endif
            </div>

            <div class="mt-20 ">
                <div class="mb-5 sub-heading">Look below for quick ecommerce shortcodes.</div>
                <div class="p-12 text-left bg-white rounded-2xl box-shadow">
                    <div x-data="{id: {{$product->id}}, style:'simple'}">
                        <div class="pick-size">Pick size...</div>
                        <div class="flex flex-wrap items-center mt-3 space-x-1 button-set">
                            <div class="cbx-btn">
                                <input type="radio" id="large" value="large" x-model="style">
                                <label for="large">Large</label>
                            </div>
                            <div class="cbx-btn">
                                <input type="radio" id="medium" value="medium" x-model="style">
                                <label for="medium">Medium</label>
                            </div>
                            <div class="cbx-btn">
                                <input type="radio" id="simple" value="simple" x-model="style" checked>
                                <label for="simple">Simple</label>
                            </div>
                            <div class="cbx-btn">
                                <input type="radio" id="detailed" value="detailed" x-model="style">
                                <label for="detailed">Detailed</label>
                            </div>
                            <div class="cbx-btn">
                                <input type="radio" id="button" value="button" x-model="style">
                                <label for="button">Button</label>
                            </div>
                            <div class="cbx-btn">
                                <input type="radio" id="in-text" value="in-text" x-model="style">
                                <label for="in-text">In-text</label>
                            </div>
                        </div>
                        <div class="mt-10">
                            <div class="mb-2 desc">And copy the shortcode into your article content...</div>
                            <div class="relative">
                                <x-common.input.input x-ref="copyfield" type="text" name="shortcode" id="shortcode"
                                    x-bind:value="`[adp product='${id}' style='${style}'/]`"
                                    style="font-family: 'Courier Prime', monospace;" />
                                <svg class="absolute cursor-pointer right-2 top-2"
                                    x-on:click="
                                        $refs.copyfield.select();
                                        $refs.copyfield.setSelectionRange(0, 99999);
                                        navigator.clipboard.writeText($refs.copyfield.value);"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.0002 20H8.00024C7.20459 20 6.44153 19.6839 5.87892 19.1213C5.31631 18.5587 5.00024 17.7956 5.00024 17V7C5.00024 6.73478 4.89489 6.48043 4.70735 6.29289C4.51981 6.10536 4.26546 6 4.00024 6C3.73503 6 3.48067 6.10536 3.29314 6.29289C3.1056 6.48043 3.00024 6.73478 3.00024 7V17C3.00024 18.3261 3.52703 19.5979 4.46471 20.5355C5.40239 21.4732 6.67416 22 8.00024 22H16.0002C16.2655 22 16.5198 21.8946 16.7074 21.7071C16.8949 21.5196 17.0002 21.2652 17.0002 21C17.0002 20.7348 16.8949 20.4804 16.7074 20.2929C16.5198 20.1054 16.2655 20 16.0002 20ZM21.0002 8.94C20.9898 8.84813 20.9697 8.75763 20.9402 8.67V8.58C20.8922 8.47718 20.828 8.38267 20.7502 8.3L14.7502 2.3C14.6676 2.22222 14.5731 2.15808 14.4702 2.11H14.3802L14.0602 2H10.0002C9.20459 2 8.44153 2.31607 7.87892 2.87868C7.31631 3.44129 7.00024 4.20435 7.00024 5V15C7.00024 15.7956 7.31631 16.5587 7.87892 17.1213C8.44153 17.6839 9.20459 18 10.0002 18H18.0002C18.7959 18 19.559 17.6839 20.1216 17.1213C20.6842 16.5587 21.0002 15.7956 21.0002 15V9V8.94ZM15.0002 5.41L17.5902 8H16.0002C15.735 8 15.4807 7.89464 15.2931 7.70711C15.1056 7.51957 15.0002 7.26522 15.0002 7V5.41ZM19.0002 15C19.0002 15.2652 18.8949 15.5196 18.7074 15.7071C18.5198 15.8946 18.2655 16 18.0002 16H10.0002C9.73503 16 9.48067 15.8946 9.29314 15.7071C9.1056 15.5196 9.00024 15.2652 9.00024 15V5C9.00024 4.73478 9.1056 4.48043 9.29314 4.29289C9.48067 4.10536 9.73503 4 10.0002 4H13.0002V7C13.0002 7.79565 13.3163 8.55871 13.8789 9.12132C14.4415 9.68393 15.2046 10 16.0002 10H19.0002V15Z" fill="#708195"/>
                                </svg>

                                <div x-text="`[adp product='${id}' style='${style}'/]`" class="with-auto text-center mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="flex items-center justify-center py-10 space-x-10 text-lg font-bold text-gray-300">
            <a class="hover:text-gray-400" href="/admin/products">Home</a>
            <a class="hover:text-gray-400" href="/admin/product_request">Product Request Form</a>
            <a class="hover:text-gray-400" href="#">Sound charts</a>
            <a class="hover:text-gray-400" href="/admin/deals">Deals dashboard</a>
            <a class="hover:text-gray-400" href="">Logout</a>
        </div>

        <div class="mt-10 text-xs font-semibold text-center text-gray-300 copyright">© 2021 Authority Media. All rights
            reserved.</div>

    </div>
</x-custom-full-layout>
