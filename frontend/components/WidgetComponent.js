import React from 'react';
import ReactDOM from 'react-dom';
import {Provider} from 'react-redux';
import domready from 'domready';
import loadJs from 'load-js';
import _trimStart from 'lodash-es/trimStart';

export default class WidgetComponent {

    constructor() {
        this.scripts = [];
        this.toRender = [];

        this._widgets = {};

        setTimeout(() => {
            const scripts = this.scripts.map(url => ({
                url,
                async: true,
            }));
            loadJs(scripts).then(() => domready(() => {
                this.toRender.forEach(args => this.render.apply(this, args));
            }));
        });
    }

    register(name, func) {
        name = _trimStart(name, '\\');
        if (arguments.length === 1) {
            // Decorator
            return func => {
                this._widgets[name] = func;
            };
        } else {
            this._widgets[name] = func;
            return func;
        }
    }

    render(elementId, name, props) {
        const store = require('components').store;
        const WidgetComponent = this._widgets[_trimStart(name, '\\')];
        ReactDOM.render(
            <Provider store={store.store}>
                <WidgetComponent {...props} />
            </Provider>,
            document.getElementById(elementId)
        );
    }

}
