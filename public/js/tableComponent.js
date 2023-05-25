function tableComponent(sort = 'id', order = 'desc') {
    return {
        checked: false,
        sort: sort,
        order: order,
        showSpoilers: [],
        check($event) {
            [...document.querySelectorAll("input.selectAllCheckable")].map((el) => {
                el.checked = $event.target.checked;
            });
        },
        applySort(targetSort) {
            let url = new URL(window.location);
            url.searchParams.delete('order');

            if (targetSort != this.sort) {
                url.searchParams.delete('sort');
                url.searchParams.append('sort', targetSort);
                url.searchParams.append('order', 'ASC');
            } else {
                if (this.order == 'ASC') {
                    url.searchParams.append('order', 'DESC');
                } else {
                    url.searchParams.append('order', 'ASC');
                }
            }
            window.location = url.href;
        },
        defaultSort() {
            let url = new URL(window.location);
            url.searchParams.delete('sort');
            url.searchParams.delete('order');
            window.location = url.href;
        },
        showSpoiler(i) {
            this.showSpoilers[i] = !this.showSpoilers[i];
        }
    }
}
