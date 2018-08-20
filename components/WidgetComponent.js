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

    add(widgets) {
        this._widgets = {
            ...this._widgets,
            ...widgets,
        };
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
        name = _trimStart(name, '\\');

        const store = require('components').store;
        const WidgetComponent = this._widgets[name];
        if (!WidgetComponent) {
            throw new Error(`Not found widget component '${name}'`);
        }

        ReactDOM.render(
            <Provider store={store.store}>
                <WidgetComponent {...props} />
            </Provider>,
            document.getElementById(elementId)
        );
    }

}
