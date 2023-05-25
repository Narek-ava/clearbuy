@props(['name', 'id' => '', 'options' => [], 'selected' => null, 'required' => false, 'default' => '-- select --'])

<select name="{{ $name }}" {{ $attributes->merge([
        'class' => 'border bg-white js-choice rounded-md px-4',
        'id' => $id
    ]) }}>
    @if ($default)
        <option placeholder {{ $required ? '' : 'value' }} {{ $selected === null ? 'selected' : '' }} value="">{{ $default }}</option>
    @endif
    @foreach ($options as $option)
        <option value="{{ $option->key }}" {{ $selected !== null && $selected == $option->key ? 'selected' : '' }}>
            {{ $option->value }}
        </option>
    @endforeach
</select>
<script>
    // Pass single element
    if('{{$id}}' != ''){
        element = document.querySelector('#{{$id}}');
        var {{$id}}_list = new Choices(element, {
            classNames: {
                containerOuter: 'choices mb-0 flex-1 ',
                containerInner: 'bg-white border rounded p-0 ',
                listSingle: 'choices__list--single px-4 py-2',
                itemChoice: 'choices__item--choice bg-gray-200 bg-opacity-25 m-1 rounded',
            },
            sorter: function(a, b) {
                return a.label - b.label;
            },
        });
    }
</script>
