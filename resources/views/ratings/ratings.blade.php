@once
    @push('headerScripts')
        <link href="{{ asset('css/nouislider.min.css') }}" rel="stylesheet" referrerpolicy="no-referrer" />
    @endpush
    @push('footerScripts')
        <script src="{{ asset('js/nouislider.js') }}" referrerpolicy="no-referrer"></script>
    @endpush
@endonce
<div class="flex" wire:ignore>
    <x-form.container-row class="flex-wrap">

            @foreach ($allRatings as $rating)
                <x-form.input class="rating-wrapper w-1/2">
                    <div class="group-name-header-label">
                        {{ $rating->name }}
                    </div>
                    <table class="rating_input_tbl">
                        <tr>
                            <td class="w-1/6">
                                <input
                                    type="text"
                                    name='ratings[{{ $rating->id }}]'
                                    id="sl_display_input_{{$rating->id}}"
                                    disabled
                                    class="rating_input"
                                    value="{{ old('ratings') ? old('ratings.'.$rating->id) : $rating->pivot->admin_rating ?? '' }}" />
                                <input
                                    type="hidden"
                                    name='ratings[{{ $rating->id }}]'
                                    id="sl_input_{{$rating->id}}"
                                    class="rating_input"
                                    value="{{ old('ratings') ? old('ratings.'.$rating->id) : $rating->pivot->admin_rating ?? '' }}" />
                            </td>
                            <td class="w-5/1 pr-20">
                                <div class="slide_item mb-5 mt-2" id="sl_div_{{$rating->id}}" data-rating_id="{{ $rating->id }}"></div>
                            </td>
                        </tr>
                    </table>
                    @push('footerScripts')
                        <script>
                            var ratingId = {{ $rating->id }};
                            var ratingVal = {{ old('ratings') ? old('ratings.'.$rating->id) : $rating->pivot->admin_rating ?? 0 }};

                            noUiSlider.create(document.getElementById('sl_div_' + ratingId), {
                                start: [ratingVal],
                                step: 0.1,
                                range: {
                                    'min': [0],
                                    'max': [10]
                                },
                                connect: [true, false],
                                pips: {
                                    mode: 'steps',
                                    density: 10,
                                    filter: (value, type) => {
                                        return 0;
                                    }
                                }
                            }).on('update', function (values, handle) {
                                if(typeof(this.target) != "undefined" && this.target !== null) {
                                    var slInput = document.getElementById('sl_input_' + $(this.target).data('rating_id'));
                                    var slDisplayInput = document.getElementById('sl_display_input_' + $(this.target).data('rating_id'));
                                } else {
                                    var slInput = document.getElementById('sl_input_' +  ratingId);
                                    var slDisplayInput = document.getElementById('sl_display_input_' +  ratingId);
                                }

                                slInput.setAttribute('value', Math.round(values * 10) / 10);
                                slDisplayInput.setAttribute('value', Math.round(values * 10) / 10);
                            });
                        </script>
                    @endpush
                </x-form.input>
            @endforeach

    </x-form.container-row>
</div>
