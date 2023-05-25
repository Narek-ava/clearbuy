@livewire ('product-form', [
    'item' => $item,
    'currencies' => $currencies ?? null,
    'categories' => $categories ?? null,
    'brands' => $brands ?? null,
    'countries' => $countries ?? null,
    'is_copy' => $is_copy ?? null,
    'attributeKinds' => $attributeKinds ?? null,
    'allRatings' => $allRatings ?? null,
    'contentTypes' => $contentTypes ?? null,
    'websites' => $websites ?? null,
    'badges' => $badges ?? null,
    'sidebarLinks' => $sidebarLinks,
    'backUrl' => $backUrl,
])