<div>
    @if ($errors->any())
        <div class="mb-1 space-y-1">
            @foreach ($errors->all() as $error)
                <x-common.alert.error>
                    {{ $error }}
                </x-common.alert.error>
            @endforeach
        </div>
    @endif
    <div class="relative drop-here">
        <span class="flex items-center">
            <svg class="mr-3" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.125 13.125C14.5057 13.125 15.625 12.0057 15.625 10.625C15.625 9.24429 14.5057 8.125 13.125 8.125C11.7443 8.125 10.625 9.24429 10.625 10.625C10.625 12.0057 11.7443 13.125 13.125 13.125Z" fill="#D2D2D2"/>
            <path d="M28.125 1.875H1.875C1.18437 1.875 0.625 2.43437 0.625 3.125V26.875C0.625 27.5656 1.18437 28.125 1.875 28.125H28.125C28.8156 28.125 29.375 27.5656 29.375 26.875V3.125C29.375 2.43437 28.8156 1.875 28.125 1.875ZM28.125 18.125L21.6919 12.6831C21.4038 12.3969 20.9881 12.47 20.7756 12.7188L14.5225 20.0137L16.875 23.5938L7.89062 16.3869C7.6425 16.1887 7.28313 16.2081 7.05813 16.4331L1.875 20.625V3.75C1.875 3.40438 2.155 3.125 2.5 3.125H27.5C27.845 3.125 28.125 3.40438 28.125 3.75V18.125Z" fill="#D2D2D2"/>
            </svg>
            <span>Click or drag your files here</span>
        </span>
        <input type="file" id="aa-image-upload" wire:model="files" class="absolute inset-0 z-40 opacity-0" multiple accept="image/*"
        x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false;"
        x-on:livewire-upload-error="isUploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress">
    </div>
</div>
