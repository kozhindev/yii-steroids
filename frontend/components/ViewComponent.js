import _isFunction from 'lodash-es/isFunction';
import _isObject from 'lodash-es/isObject';

export default class ViewComponent {

    constructor() {
        this.views = {};
    }

    add(views) {
        // require.context()
        if (_isFunction(views) && _isFunction(views.keys)) {
            views.keys().forEach(fileName => {
                const matches = fileName.match(/^\.\/(.*\/)?[^\/]+\/([^\/]+)\.js$/);
                const path = (matches[1] || '').replace(/\//g, '.') + matches[2];

                this.views[path] = views(fileName).default;
            });
        } else if (_isObject(views)) {
            // object
            this.views = {
                ...this.views,
                ...views,
            };
        } else {
            throw new Error('Unsupported views format for add in ViewComponent.');
        }
    }

    get(path) {
        if (!this.views[path]) {
            throw new Error('Not found view by path "' + path + '".');
        }
        return this.views[path];
    }

}
