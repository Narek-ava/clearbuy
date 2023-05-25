<div>
    <p class="px-4 mb-5 text-lg font-medium leading-6 text-gray-500 ">Click once to select | click twice to feature (6 max) | click again to deselect.</p>
    <div class="grid grid-cols-2 gap-8 px-4 attributes-list">
        @foreach ($kinds as $kind)
        <div>
            <h3 class="text-2xl font-bold leading-7 ">{{$kind->name}}</h3>
            <div class="flex flex-col justify-center mt-4">
                @if ($kind->groups->count() === 0)
                <x-form.input class="text-bold">
                    There are no attributes of this kind
                </x-form.input>
                @endif
                @foreach ($kind->groups as $group)
                    <div class="mt-3 mb-3 text-lg font-bold text-gray-500">
                        {{ $group->name}}
                    </div>
                    @foreach ($group->attributes as $attribute)
                    <div
                        x-data="{is_checked:{{ ( $ownAttributes->contains($attribute->id) || $inheritedAttributes->contains($attribute->id) ) ? 'true' : 'false' }}, is_featured:{{
                    $featuredAttributes->contains($attribute->id) ? 'true' : 'false' }}, showAbs: {{ ($ownAttributes->contains($attribute->id) === true &&  $featuredAttributes->contains($attribute->id) === false) ? 'true' : 'false'}}}">
                        <div class="relative flex flex-row-reverse items-center justify-between mb-3 cursor-pointer attributes-list-item"
                            @click="if($refs.mainAttr) { $refs.mainAttr.checked = !is_checked; is_checked = !is_checked; }  $refs.featured_input.checked = false; showAbs = true;">
                            <template x-if="(showAbs && is_checked)">
                                <div class="absolute top-0 bottom-0 left-0 right-0 "
                                    @click.stop="$refs.featured_input.checked = true; showAbs = false;"></div>
                            </template>
                            @if ($inheritedAttributes->contains($attribute->id))
                            <span class="text-gray-700">{{ $attribute->name }} (inherited)</span>
                            <div class="checkbox__control">
                                <input id="{{ str_replace(' ','_',$attribute->name) }}" type="hidden" name="attribute_ids[]"
                                value="{{ $attribute->id }}">
                                <input type="checkbox" checked disabled>
                                <div class="checkbox__control__indicator"></div>
                            </div>

                            @else
                                <div class="checkbox__control">
                                    <input x-ref="mainAttr" id="{{ str_replace(' ','_',$attribute->name) }}" class="z-10 "
                                    type="checkbox" name="attribute_ids[]" value="{{ $attribute->id }}" {{
                                    $ownAttributes->contains($attribute->id) ? 'checked' : '' }}>
                                    <div class="checkbox__control__indicator"></div>
                                </div>

                            @endif
                            <input type="checkbox" class="hidden" name="featured_attributes[]" value="{{ $attribute->id }}"
                                {{ $featuredAttributes->contains($attribute->id) ? 'checked' : '' }} x-ref="featured_input">

                            <div class="label" :class="{'label':true, 'featured': $refs.featured_input.checked}"
                                for="{{ str_replace(' ','_',$attribute->name) }}">
                                {{ $attribute->name }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
        @endforeach

    </div>
</div>
