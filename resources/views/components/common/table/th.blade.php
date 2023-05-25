<th {{ $attributes->merge(['class' => 'px-4 py-2 font-grotesk capitalize text-left font-semibold text-lg']) }}>
    @if (isset($search))
        <div class="relative float-right">
            <x-common.input.input 
                id="{{ isset($productSearch) ? 'product-search' : 'search' }}"
                value="{{ isset($productSearch) ? request()->get('search') : '' }}"
                type="text"  
                placeholder="Search..."
                class="text-gray-600 bg-gray-100" />

            <svg class="absolute top-2 right-2 {{ isset($productSearch) ? 'start-search cursor-pointer' : '' }}" 
                width="24" height="24" viewBox="0 0 24 24"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3"
                    d="M14.2929 16.7071C13.9024 16.3166 13.9024 15.6834 14.2929 15.2929C14.6834 14.9024 15.3166 14.9024 15.7071 15.2929L19.7071 19.2929C20.0976 19.6834 20.0976 20.3166 19.7071 20.7071C19.3166 21.0976 18.6834 21.0976 18.2929 20.7071L14.2929 16.7071Z"
                    fill="black" />
                <path
                    d="M11 16C13.7614 16 16 13.7614 16 11C16 8.23858 13.7614 6 11 6C8.23858 6 6 8.23858 6 11C6 13.7614 8.23858 16 11 16ZM11 18C7.13401 18 4 14.866 4 11C4 7.13401 7.13401 4 11 4C14.866 4 18 7.13401 18 11C18 14.866 14.866 18 11 18Z"
                    fill="black" />
            </svg>

        </div>
    @endif
    {{ $slot }}
</th>
