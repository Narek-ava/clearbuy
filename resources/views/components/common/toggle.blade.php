<label class="toggle">
    <input type="checkbox"  wire:click="changeit"  wire:model="state"  />
    <span class="toggle-slider round" >
        <span class="left-toggle-text" >{{ $inactive ?? '' }}</span>
        <span class="right-toggle-text" >{{ $active ?? '' }}</span>
    </span>
</label>

@push('footerScripts')
    <script>
        window.addEventListener('toggle-update', event => {
            window.location.reload();
        });
    </script>
@endpush
