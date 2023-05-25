
<div x-data="{show: true}"
    x-show="show" {{ $attributes->merge(['class' => 'toast-notification cursor-pointer z-10 fixed bottom-15 py-3 pl-6 pr-4 rounded-full font-semibold text-white text-lg text-center flex justify-between max-w-2xl bg-opacity-75 mx-auto right-0 left-0 lg:left-48']) }}
    x-transition:enter="transition ease-out origin-bottom duration-200"
    x-transition:enter-start="opacity-0 transform translate-y-full"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition origin-top ease-in duration-100"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-full"
    class="max-w-sm p-5 ml-2 border border-gray-200 rounded shadow-sm"

    x-init="()=> { setTimeout(() => {
        show = false;
    }, 1000 * 10) }">
    {{ $slot }}

    <svg class="" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" @click="show = false">
     <path d="M16.2426 6.34311L6.34309 16.2426C5.95257 16.6331 5.95257 17.2663 6.34309 17.6568C6.73362 18.0473 7.36678 18.0473 7.75731 17.6568L17.6568 7.75732C18.0473 7.36679 18.0473 6.73363 17.6568 6.34311C17.2663 5.95258 16.6331 5.95258 16.2426 6.34311Z" fill="white"/>
     <path d="M17.6569 16.2426L7.7574 6.34309C7.36688 5.95257 6.73371 5.95257 6.34319 6.34309C5.95266 6.73362 5.95266 7.36678 6.34319 7.75731L16.2427 17.6568C16.6332 18.0473 17.2664 18.0473 17.6569 17.6568C18.0474 17.2663 18.0474 16.6331 17.6569 16.2426Z" fill="white"/>
   </svg>
</div>
