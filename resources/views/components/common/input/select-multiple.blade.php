@props(['name', 'id' => '', 'options' => [], 'selected' => []])

<select multiple name="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'border bg-white js-choice rounded-md px-4',
        'id' => $id
    ]) }}>
    @foreach ($options as $option)
        <option value="{{ $option->key }}"
            {{ collect($selected)->contains($option->key) ? 'selected' : '' }}>{{ $option->value }}</option>
    @endforeach
</select>
<script>
    if('{{$id}}' != ''){
        element = document.querySelector('#{{$id}}');
        new Choices(element, {
            removeItemButton: true,
            classNames: {
                containerOuter: 'choices mb-0 flex-1',
                containerInner: 'bg-white border rounded p-0',
                listSingle: 'choices__list--single px-4 py-2',
                itemChoice: 'choices__item--choice bg-gray-200 bg-opacity-25 m-1 rounded',
                input: 'choices__input',
            }
        });
    }
</script>
