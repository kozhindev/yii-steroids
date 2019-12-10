import React from 'react';
import _set from 'lodash-es/set';
import _merge from 'lodash-es/merge';
import _cloneDeep from 'lodash-es/cloneDeep';
import _isArray from 'lodash-es/isArray';
import _isEqual from 'lodash-es/isEqual';

export default class HeadComponent {

    constructor() {
        this.titleSeparator = ' | ';
        this.initialParams = null;

        this._hocTitle = null;
        this._routeTitle = null;
        this._breadcrumbTitles = null;

        this._ssrData = {};
        this._defaultMeta = {
            'og:type': 'website',
        };
    }

    /**
     * Config params:
     *  title
     *  description
     *  image
     *  images
     *  meta{}
     *
     * @param configFunc
     * @returns {function}}
     */
    hoc(configFunc) {
        const seo = this;
        return WrappedComponent => class SeoHOC extends React.Component {

            static WrappedComponent = WrappedComponent;

            constructor() {
                super(...arguments);

                this._params = seo.initialParams ? _cloneDeep(seo.initialParams) : {};
            }

            UNSAFE_componentWillMount() {
                this._onUpdate();
            }

            componentDidUpdate() {
                this._onUpdate();
            }

            componentWillUnmount() {
                seo.setTitle(null);
            }

            render() {
                return (
                    <WrappedComponent {...this.props}/>
                );
            }

            _onUpdate() {
                const prevParams = this._params || {};
                const prevMeta = _merge({}, this._defaultMeta, prevParams.meta);
                const nextParams = configFunc(this.props) || {};
                const nextMeta = _merge({}, this._defaultMeta, nextParams.meta);

                // Update document title
                if (prevParams.title !== nextParams.title
                    || prevMeta['og:title'] !== nextMeta['og:title']
                    || prevMeta['twitter:title'] !== nextMeta['twitter:title']
                ) {
                    seo.setTitle(nextParams.title);
                    seo.setMeta('og:title', nextMeta['og:title'] || nextParams.title);
                    seo.setMeta('twitter:title', nextMeta['twitter:title'] || nextMeta['og:title'] || nextParams.title);
                }

                // Update description
                if (prevParams.description !== nextParams.description
                    || prevMeta['og:description'] !== nextMeta['og:description']
                    || prevMeta['twitter:description'] !== nextMeta['twitter:description']
                ) {
                    seo.setMeta('og:description', nextMeta['og:description'] || nextParams.description);
                    seo.setMeta('twitter:description', nextMeta['twitter:description'] || nextMeta['og:description'] || nextParams.description);
                }

                // Update images
                const prevImages = [].concat(prevParams.image || prevParams.images || []);
                const nextImages = [].concat(nextParams.image || nextParams.images || []);
                if (!_isEqual(prevImages, nextImages)) {
                    seo.setMeta('og:image', nextImages);
                }

                // Save params
                if (!_isEqual(prevParams, nextParams)) {
                    this._params = nextParams;
                }
            }

        };
    }

    setRouteTitle(value) {
        this._routeTitle = value;
        this._updateTitle();
    }

    setBreadcrumbTitles(value) {
        this._breadcrumbTitles = [].concat(value || []).join(this.titleSeparator);
        this._updateTitle();
    }

    setTitle(value) {
        this._hocTitle = value;
        this._updateTitle();
    }

    _updateTitle() {
        const title = [
            this._hocTitle || this._routeTitle,
            this._breadcrumbTitles,
        ]
            .filter(Boolean)
            .join(this.titleSeparator);

        if (process.env.IS_SSR) {
            _set(this._ssrData, 'title', title);
        } else {
            document.title = title;
        }
    }

    setMeta(property, value) {
        if (process.env.IS_SSR) {
            _set(this._ssrData, ['meta', property], value);
        } else {
            const headElement = document.head || document.querySelector('head');
            const selector = `meta[property="${property}"]`;

            if (_isArray(value)) {
                headElement.querySelectorAll(selector).forEach(node => {
                    node.parentNode.removeChild(node);
                });

                value.forEach(item => {
                    const node = document.createElement('meta');
                    node.setAttribute('content', item);
                    headElement.appendChild(node);
                });
            } else {
                let node = headElement.querySelector(selector);
                if (!node) {
                    node = document.createElement('meta');
                    headElement.appendChild(node);
                }
                node.setAttribute('content', value);
            }
        }
    }

}
