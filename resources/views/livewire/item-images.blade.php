@once
    @push('headerScripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css"
        integrity="sha512-+EoPw+Fiwh6eSeRK7zwIKG2MA8i3rV/DGa3tdttQGgWyatG/SkncT53KHQaS5Jh9MNOT3dmFL0FjTY08And/Cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush

    @push('footerScripts')
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"
            integrity="sha512-IsNh5E3eYy3tr/JiX2Yx4vsCujtkhwl7SLqgnwLNgf04Hrt9BT9SXlLlZlWx+OK4ndzAoALhsMNcCmkggjZB1w=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush
@endonce

<div class="space-y-2">
    <div class="flex flex-wrap w-4/5 m-auto product-images-container">
        <div class="flex w-full pb-5">
            <div class="flex w-1/2 text-base opacity-25">Drag to reorder</div>
            <div class="flex justify-end w-1/2">
                <x-common.a.a href="#" wire:click.prevent="removeSelected()"
                    onclick="enableDragSort('drag-sort-enable')" class="text-red-500 opacity-25">
                    Delete selected
                </x-common.a.a>
            </div>
        </div>
        <div class="flex w-full pb-5">
            @if(isset($asin) && !empty($asin))
                @livewire('product-amazon-image', ['asin' => $asin ?? ''])
            @endif
        </div>
        <div class="flex flex-wrap items-start gap-4 images-container drag-sort-enable">
            @if ($images->count() > 0)
                @foreach ($images as $index => $image)
                    <div class="relative px-3 py-2 overflow-hidden bg-center bg-no-repeat bg-contain rounded-sm w-44 h-44 ordered-image group image-group"
                        style="background-image: url('{{ $image->url }}');background-size: 176px 176px;" id="{{ $index }}">
                        <x-common.input.checkbox wire:model.defer="items2Delete" value="{{ $index }}" class="opacity-75" />
                        <input type="hidden" name="{{$name}}" value="{{$image->path}}">
                        <div class="absolute flex space-x-3 action">
                            <div href="{{ $image->url }}"
                                class="grid w-10 h-10 bg-white bg-opacity-75 rounded-full cursor-pointer hover:bg-opacity-100 place-items-center zoom-in">
                            </div>
                            <div wire:click.prevent="remove( {{ $index }})"
                                class="grid w-10 h-10 bg-white bg-opacity-75 rounded-full cursor-pointer hover:bg-opacity-100 place-items-center delete-it">
                            </div>
                        </div>
                    </div>
                @endforeach

                @once
                    @push('footerScripts')
                        <script>
                            jQuery(document).ready(function() {
                               jQuery('.zoom-in').magnificPopup({type:'image'});
                           });
                        </script>
                    @endpush
                @endonce

            @endif
            <div class="flex" x-data="fileSystem()">

                <x-common.button.a class="w-44 h-44 product-images-add-new" x-on:click.prevent="show = true" href="#" wire:click="updateFileSystem">
                    <div class="m-auto text-center align-middle"><i class="fa fa-plus" aria-hidden="true"></i></div>
                </x-common.button.a>

                <div class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50" x-show="show"
                    style="display: none;">
                    <div class="relative w-3/4 bg-white rounded-2xl">
                        <span x-on:click="show = false"
                            class="absolute top-0 right-0 mx-3 text-2xl cursor-pointer close-popup">&times;</span>

                        <div class="flex flex-col divide-y px-7 image-upload-popup">
                            <div class="flex justify-between px-4 my-4 space-y-2 text-cool-gray-500">

                                <x-common.button.group class="justify-end">
                                    <a href="#" class="flex items-center space-x-4 font-semibold"
                                        wire:click.prevent="back" title="back"
                                        style="{{ $path == '' ? 'display: none;' : '' }}">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3"
                                                d="M8.8181 7.90379C8.42757 7.51327 7.79441 7.51327 7.40388 7.90379C7.01336 8.29432 7.01336 8.92748 7.40388 9.31801L15.8892 17.8033C16.2797 18.1938 16.9129 18.1938 17.3034 17.8033C17.6939 17.4128 17.6939 16.7796 17.3034 16.3891L8.8181 7.90379Z"
                                                fill="#708195" />
                                            <path
                                                d="M8.40381 16.3891C8.40381 16.9414 7.95609 17.3891 7.40381 17.3891C6.85152 17.3891 6.40381 16.9414 6.40381 16.3891V7.9038C6.40381 7.36841 6.82549 6.928 7.36037 6.90475L15.4921 6.55119C16.0439 6.5272 16.5106 6.95505 16.5346 7.50681C16.5586 8.05858 16.1307 8.52532 15.579 8.54931L8.40381 8.86127V16.3891Z"
                                                fill="#708195" />
                                        </svg>
                                        <span>Up one level</span>
                                    </a>
                                </x-common.button.group>

                                <div class="flex items-center space-x-7">
                                    <div>Current location: <span class="underline ">/{{ $path }}</span></div>
                                    <div class="relative ">
                                        <input class="w-full px-4 py-2 text-gray-600 bg-gray-100 border rounded-md"
                                            type="text" id="search-images" placeholder="Search...">
                                        <svg class="absolute top-2 right-2" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3"
                                                d="M14.2929 16.7071C13.9024 16.3166 13.9024 15.6834 14.2929 15.2929C14.6834 14.9024 15.3166 14.9024 15.7071 15.2929L19.7071 19.2929C20.0976 19.6834 20.0976 20.3166 19.7071 20.7071C19.3166 21.0976 18.6834 21.0976 18.2929 20.7071L14.2929 16.7071Z"
                                                fill="black"></path>
                                            <path
                                                d="M11 16C13.7614 16 16 13.7614 16 11C16 8.23858 13.7614 6 11 6C8.23858 6 6 8.23858 6 11C6 13.7614 8.23858 16 11 16ZM11 18C7.13401 18 4 14.866 4 11C4 7.13401 7.13401 4 11 4C14.866 4 18 7.13401 18 11C18 14.866 14.866 18 11 18Z"
                                                fill="black"></path>
                                        </svg>

                                    </div>
                                </div>

                            </div>
                            <div class="flex-grow overflow-auto">
                                <div x-ref="fileSystem"
                                    class="grid items-start grid-cols-2 gap-4 p-4 images-container lg:grid-cols-6 md:grid-cols-3">
                                    @foreach ($folders as $folder)
                                    <div class="relative flex flex-col justify-center space-y-1"
                                        id="path-{{ $folder->path }}">
                                        <div class="grid w-full bg-gray-100 rounded-sm item-folder place-items-center h-44"
                                            wire:click="folder('{{ $folder->path }}')">
                                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M9 42H43C44.6569 42 46 40.6569 46 39V17C46 15.3431 44.6569 14 43 14H22L16.8787 8.87868C16.3161 8.31607 15.553 8 14.7574 8H9C7.34315 8 6 9.34315 6 11V39C6 40.6569 7.34315 42 9 42Z"
                                                    fill="#6B7280" fill-opacity="0.5" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5 38H39C40.6569 38 42 36.6569 42 35V13C42 11.3431 40.6569 10 39 10H18L12.8787 4.87868C12.3161 4.31607 11.553 4 10.7574 4H5C3.34315 4 2 5.34315 2 7V35C2 36.6569 3.34315 38 5 38Z"
                                                    fill="#6B7280" fill-opacity="0.5" />
                                            </svg>
                                        </div>
                                        <label class="block">
                                            <div class="absolute top-2 left-2">
                                                <div class="checkbox__control">
                                                    <input type="checkbox"
                                                    id="{{ rand() }}" wire:model.defer="selected_items"
                                                    value="{{ $folder->path }}">
                                                    <div class="opacity-75 checkbox__control__indicator"></div>
                                                </div>
                                            </div>
                                            <span>{{ $folder->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                    @foreach ($files as $index=>$file)
                                    <div class="h-full image-group" id="path-{{ $file->path }}">
                                        <div class="relative h-40 p-1">
                                            <label class="w-40 overflow-hidden ">
                                                <img src="{{ $file->url }}" alt="" class="object-cover h-full">
                                                <div class="absolute top-2 left-2">
                                                    <div class="checkbox__control">
                                                        <input type="checkbox"
                                                        id="{{ rand() }}"
                                                        wire:model.defer="selected_items" value="{{ $file->path }}">
                                                        <div class="opacity-75 checkbox__control__indicator"></div>
                                                    </div>
                                                </div>
                                            </label>
                                            <span class="name">{{ $file->name }}</span>
                                            <div class="absolute flex space-x-3 action">
                                                <div href="{{ $file->url }}"
                                                    class="grid w-10 h-10 bg-white bg-opacity-75 rounded-full cursor-pointer hover:bg-opacity-100 place-items-center zoom-in">
                                                </div>
                                                <div x-on:click="showConfirmDeleteItem($wire, '{{ $file->path }}');"
                                                    class="grid w-10 h-10 bg-white bg-opacity-75 rounded-full cursor-pointer hover:bg-opacity-100 place-items-center delete-it">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <label
                                        class="flex flex-col justify-center space-y-1 break-words cursor-pointer"
                                        id="path-{{ $file->path }}">
                                        <div
                                            class="relative w-full overflow-hidden bg-center bg-no-repeat bg-cover rounded-sm image-group h-44 ordered-image group">
                                            <img src="{{ $file->url }}" alt="" class="absolute object-cover h-full">
                                            <input type="checkbox" id="{{ rand() }}" wire:model.defer="selected_items"
                                                value="{{ $file->path }}" class="absolute opacity-75 top-2 left-2">
                                            <div class="absolute flex space-x-3 action">
                                                <div
                                                    class="grid w-10 h-10 bg-white bg-opacity-75 rounded-full cursor-pointer hover:bg-opacity-100 place-items-center zoom-in">
                                                </div>
                                                <div x-on:click.prevent="showConfirmDeleteItem($wire, '{{ $file->path }}')"
                                                    class="grid w-10 h-10 bg-white bg-opacity-75 rounded-full cursor-pointer hover:bg-opacity-100 place-items-center delete-it">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <span>{{ $file->name }}</span>
                                        </div>
                                    </label> --}}
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center justify-between py-7 ">
                                <x-common.a.a href="#" x-on:click.prevent="showConfirmDelete($wire, $refs)"
                                    class="text-red-400 hover:text-secondary">
                                    delete selected
                                </x-common.a.a>
                                <x-common.button.group class="justify-end space-x-4 text-sm">
                                    <x-common.a.a class="tracking-wider " href="#"
                                        x-on:click.prevent="showCreateFolder($refs)">
                                        New folder
                                    </x-common.a.a>
                                    <x-common.a.a class="tracking-wider " href="#"
                                        x-on:click.prevent="showUploadFilesModal = true">
                                        Upload files
                                    </x-common.a.a>
                                    <x-common.button.a href="#" x-on:click.prevent="select($wire, $refs)">
                                        Select
                                    </x-common.button.a>
                                </x-common.button.group>
                            </div>
                        </div>

                        <div x-show="showDeleteModal"
                            class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemModal sm:py-28">
                            <div @click.away="showDeleteModal = false"
                                class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
                                <span @click="showDeleteModal = false"
                                    class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>

                                <div class="p-10 ">
                                    <p class="text-lg font-semibold">Delete selected items? </p>
                                    <x-common.button.group
                                        class="flex flex-row flex-no-wrap items-center justify-center mt-10 space-x-2">
                                        <x-common.button.a href="#" type="alt" @click.prevent="showDeleteModal = false">
                                            Cancel
                                        </x-common.button.a>
                                        <x-common.button.a href="#" @click.prevent="confirmDelete($wire)">
                                            <svg wire:loading wire:target="delete"
                                                class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span wire:loading.remove wire:target="delete">Yes</span>
                                        </x-common.button.a>
                                    </x-common.button.group>
                                </div>

                            </div>
                        </div>

                        <div x-show="showDeleteItemModal"
                            class="fixed top-0 left-0 z-20 w-full h-full p-4 bg-black bg-opacity-50 deleteItemModal sm:py-28">
                            <div @click.away="showDeleteItemModal = false"
                                class="relative w-full mx-auto text-center bg-white rounded popup-shadow sm:w-3/4 md:w-1/4">
                                <span @click="showDeleteItemModal = false"
                                    class="absolute top-0 right-0 mx-3 text-2xl font-bold cursor-pointer">&times;</span>
                                <div class="p-10 ">
                                    <p class="text-lg font-semibold">Delete selected item? </p>
                                    <x-common.button.group
                                        class="flex flex-row flex-no-wrap items-center justify-center mt-10 space-x-2">
                                        <x-common.button.a href="#" type="alt"
                                            @click.prevent="showDeleteItemModal = false">
                                            Cancel
                                        </x-common.button.a>
                                        <x-common.button.a href="#" @click.prevent="confirmDeleteItem($wire)">
                                            <svg wire:loading wire:target="deleteItem"
                                                class="w-5 h-5 mr-3 -ml-1 text-white animate-spin"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span wire:loading.remove wire:target="deleteItem">Yes</span>
                                        </x-common.button.a>
                                    </x-common.button.group>
                                </div>

                            </div>
                        </div>

                        <div x-show="showCreateFolderModal"
                            class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="p-6 pb-4 bg-white" x-on:click.away="showCreateFolderModal = false">
                                <p>Enter folder name: </p>
                                <x-common.input.input x-ref="folderName" name="" value="" class="mb-1"
                                    x-on:keydown.enter.prevent="createFolder($wire, $refs)" placeholder="Folder name" />
                                <x-common.button.group class="justify-end">
                                    <x-common.button.a href="#" x-on:click.prevent="createFolder($wire, $refs)">
                                        Create
                                    </x-common.button.a>
                                    <x-common.a.a href="#" x-on:click.prevent="showCreateFolderModal = false">
                                        Cancel
                                    </x-common.a.a>
                                </x-common.button.group>
                            </div>
                        </div>

                        <div x-show="showUploadFilesModal"
                            class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">

                            <div @click.away="showUploadFilesModal = false"
                            class="relative w-3/12 p-6 pb-4 bg-white file-upload-popup"
                                >

                                <span @click.prevent="showUploadFilesModal = false"
                                    class="absolute top-0 right-0 mx-3 text-2xl cursor-pointer close-popup">&times;</span>

                                <div x-data="{ isUploading: false, progress: 0, currentFile:'' }">

                                    <div class="text-lg font-semibold text-center mb-7 ">Add images - jpg | png | webp</div>
                                    <div class="my-2">
                                        @livewire('image-upload', ['path' => $path ?? '','product_id' => $product_id ?? '','asin' => $asin ?? ''], key('fileUpload'))
                                    </div>

                                    <!-- Progress Bar -->
                                    <div x-show="isUploading" class="relative pt-2">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span
                                                    class="inline-block px-2 py-1 text-xs font-semibold text-gray-600 ">
                                                    Uploading...
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-block text-xs font-semibold text-gray-600"
                                                    x-text="`${progress}%`">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex h-1 mb-4 overflow-hidden text-xs bg-gray-200 rounded">
                                            <div x-bind:style="`width:${progress}%`"
                                                class="flex flex-col justify-center text-center text-white shadow-none bg-primary whitespace-nowrap">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    @once
                        @push('footerScripts')
                            <script>
                                jQuery(document).ready(function() {
                                   jQuery('.zoom-in').magnificPopup({type:'image'});
                               });
                            </script>
                        @endpush
                    @endonce
                </div>
            </div>
        </div>
    </div>
    {{-- @if ($images->count() > 0)--}}
    {{-- <table>--}}
        {{-- @foreach ($images as $index => $image)--}}
        {{-- <tr>--}}
            {{-- <x-common.table.td>--}}
                {{-- <div class="w-16 h-16 bg-center bg-no-repeat bg-contain"
                    style="background-image: url('{{ $image->url }}')">--}}
                    {{-- <input type="hidden" name="{{ $name }}" value="{{ $image->path}}">--}}
                    {{-- </div>--}}
                {{-- </x-common.table.td>--}}
            {{-- <x-common.table.td>--}}
                {{-- <span>--}}
                    {{-- {{ $image->path}}--}}
                    {{-- </span>--}}
                {{-- </x-common.table.td>--}}

            {{-- @if ($images->count() > 1)--}}
            {{-- <x-common.table.td>--}}
                {{-- <a class="block px-2 py-0.5" href="#" wire:click.prevent="moveUp({{ $index }})"
                    title="move up">--}}
                    {{--
                    <x-common.arrow.sort-up class="border-black" />--}}
                    {{--
                </a>--}}
                {{-- </x-common.table.td>--}}
            {{-- <x-common.table.td>--}}
                {{-- <a class="block px-2 py-0.5" href="#" wire:click.prevent="moveDown({{ $index }})"
                    title="move down">--}}
                    {{--
                    <x-common.arrow.sort-down class="border-black" />--}}
                    {{--
                </a>--}}
                {{-- </x-common.table.td>--}}
            {{-- @endif--}}

            {{-- <x-common.table.td>--}}
                {{-- <a class="block px-2 py-0.5" href="#" wire:click.prevent="remove({{ $index }})" title="remove">--}}
                    {{-- <span class="text-lg font-bold">&times;</span>--}}
                    {{-- </a>--}}
                {{-- </x-common.table.td>--}}
            {{-- </tr>--}}
        {{-- @endforeach--}}
        {{-- </table>--}}
    {{-- @endif--}}
</div>
@once
    @push('footerScripts')
        <script>
            function fileSystem() {
                let obj = {
                    show: false,
                    showDeleteModal: false,
                    showDeleteItemModal: false,
                    showCreateFolderModal: false,
                    showUploadFilesModal: false,
                    select($wire, $refs) {
                        $wire.select().then(() => {
                            this.show = false;
                            enableDragSort('drag-sort-enable');
                            [...$refs.fileSystem.querySelectorAll("input[type='checkbox']")].forEach((item) => {
                                item.checked = false;
                            });
                        });
                    },
                    showConfirmDelete($wire, $refs) {
                        //console.log($refs.fileSystem.querySelectorAll("input[type='checkbox']:checked"));
                        if ([...$refs.fileSystem.querySelectorAll("input[type='checkbox']:checked")].length > 0) {
                            this.showDeleteModal = true;
                        }
                    },
                    showConfirmDeleteItem($wire, $path) {
                        if ($path !== '') {
                           $wire.set('item2Delete', $path);
                            this.showDeleteItemModal = true;
                        }
                    },
                    confirmDelete($wire) {
                        $wire.delete().then(() => {
                            this.showDeleteModal = false;
                        });
                    },
                    confirmDeleteItem($wire) {
                        $wire.deleteItem().then(() => {
                            this.showDeleteItemModal = false;
                        });
                    },
                    showCreateFolder($refs) {
                        this.showCreateFolderModal = true;
                        setTimeout(() => {
                            $refs.folderName.focus();
                        }, 100);
                    },
                    createFolder($wire, $refs) {
                        if ($refs.folderName.value.length > 0) {
                            $wire.createFolder($refs.folderName.value).then(() => {
                                $refs.folderName.value = "";
                                this.showCreateFolderModal = false;
                            });
                        }
                    },
                    filesUploaded() {
                        this.showUploadFilesModal = false;
                    }
                }
                Livewire.on("filesUploaded", () => {
                    obj.filesUploaded();
                });
                return obj;
            }

            function enableDragSort(listClass) {
                const sortableLists = document.getElementsByClassName(listClass);
                // console.log(sortableLists);
                Array.prototype.map.call(sortableLists, (list) => {enableDragList(list)});
            }

            function enableDragList(list) {
                Array.prototype.map.call(list.children, (item) => {enableDragItem(item)});
            }

            function enableDragItem(item) {
                item.setAttribute('draggable', true)
                item.ondrag = handleDrag;
                item.ondragend = handleDrop;
            }

            function handleDrag(item) {
                const selectedItem = item.target,
                    list = selectedItem.parentNode,
                    x = event.clientX,
                    y = event.clientY;

                selectedItem.classList.add('drag-sort-active');
                let swapItem = document.elementFromPoint(x, y) === null ? selectedItem : document.elementFromPoint(x, y);

                if (list === swapItem.parentNode) {
                    swapItem = swapItem !== selectedItem.nextSibling ? swapItem : swapItem.nextSibling;
                    list.insertBefore(selectedItem, swapItem);
                }
            }

            function handleDrop(item) {
                item.target.classList.remove('drag-sort-active');
                document.dispatchEvent(orderImagesEvent);
            }

            const orderImagesEvent = new Event('livewire:orderImages');

            document.addEventListener('livewire:orderImages', function () {
                let sortableLists = document.getElementsByClassName('ordered-image');
                sortableLists = Array.from(sortableLists).map(function(item) {
                    return item.id;
                });
                @this.orderImages(sortableLists).then(() => { enableDragSort('drag-sort-enable'); });
            });

            // Search media
            document.getElementById("search-images").addEventListener('keyup', function() {
                var filter =  this.value.trim().replace(/ +/g, ' ').toLowerCase();

                document.querySelectorAll('.images-container > div,.images-container > label').forEach(elem => {
                    var text = elem.id.split('path-').join('').replace(/[\s]+/g, ' ');
                    if (text) {
                        if (text.toLowerCase().includes(filter)) {
                            elem.style.display = "";
                        } else {
                            elem.style.display = "none";
                        }
                    }
                });
            });

        </script>
    @endpush
@endonce
