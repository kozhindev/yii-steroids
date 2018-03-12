import _merge from 'lodash/merge';

export default class ViewComponent {

    constructor() {
        this.views = {};
    }

    add(views) {
        this.views = _merge(this.views, views);
    }

}
