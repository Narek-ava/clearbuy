<table {{ $attributes->merge(['class' => 'table table-auto w-full border-collapse']) }}>
    @if (isset($thead))
        <thead class="font-semibold text-black bg-white h-13 thead-shadow">
            <tr>
                {{ $thead }}
            </tr>
        </thead>
    @endif    
    <tbody>
        {{ $slot }}
    </tbody>
</table>
