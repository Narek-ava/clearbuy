<ul>
    @foreach ($sidebarLinks as $linkGroup)
        <li x-data="{ show: {{ $linkGroup->active ? 'true' : 'false' }} }" class="tracking-widest uppercase {{ $linkGroup->active ? ' text-opacity-100' : 'text-opacity-25 hover:text-opacity-100' }}">
            <a href="{{ $linkGroup->path ?? '#' }}" class="flex flex-row flex-no-wrap items-center px-6 py-3 space-x-2 uppercase"
               x-bind:class="{ 'text-white bg-primary': show, 'text-gray-300 hover:text-primary': !show }"
                @if(!empty($linkGroup->items))
                    x-on:click.prevent="show = !show">
                @endif
                <span>{{ $linkGroup->name }}</span>
            </a>
            @if(isset($linkGroup->items))
                <ul x-show="show" class="mb-5 text-sm bg-primary bg-opacity-10">
                    @foreach ($linkGroup->items as $link)
                        <li class="text-white {{ $link->active ? ' text-opacity-100' : 'text-opacity-25 hover:text-opacity-100' }} pl-2">
                            <a href="{{ $link->path }}" class="block px-6 py-3">{{ $link->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
