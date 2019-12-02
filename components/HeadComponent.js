import React from 'react';
import {connect} from 'react-redux';
import _set from 'lodash-es/set';
import _merge from 'lodash-es/merge';
import _cloneDeep from 'lodash-es/cloneDeep';
import _isArray from 'lodash-es/isArray';
import _isEqual from 'lodash-es/isEqual';
import {getBreadcrumbs} from '../reducers/navigation';

export default class HeadComponent {

    constructor() {
        this.siteName = null;
        this.titleSeparator = ' | ';
        this.initialParams = null;

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
        const stateMap = state => {
            const breadcrumbs = getBreadcrumbs(state);
            const pageItem = breadcrumbs.pop();
            const parentItems = breadcrumbs.filter(item => item.isDocumentTitleVisible !== false);

            return {
                pageTitle: pageItem && pageItem.isDocumentTitleVisible !== false && pageItem.title || null,
                parentTitles: parentItems.filter(Boolean).join(this.titleSeparator),
            };
        };
        return WrappedComponent => @connect(stateMap)
        class SeoHOC extends React.Component {

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

            render() {
                return (
                    <WrappedComponent {...this.props}/>
                );
            }

            _onUpdate(prevProps) {
                const prevParams = this._params || {};
                const prevMeta = _merge({}, this._defaultMeta, prevParams.meta);
                const nextParams = configFunc(this.props) || {};
                const nextMeta = _merge({}, this._defaultMeta, nextParams.meta);

                // Update document title
                if (prevParams.title !== nextParams.title
                    || prevMeta['og:title'] !== nextMeta['og:title']
                    || prevMeta['twitter:title'] !== nextMeta['twitter:title']
                    || prevProps.pageTitle !== this.props.pageTitle
                    || prevProps.parentTitles !== this.props.parentTitles
                ) {
                    const title = [
                        nextParams.title || this.props.pageTitle,
                        this.props.parentTitles,
                        this.siteName,
                    ]
                        .filter(Boolean)
                        .join(seo.titleSeparator);

                    seo.setTitle(title);
                    seo.setMeta('og:title', nextMeta['og:title'] || title);
                    seo.setMeta('twitter:title', nextMeta['twitter:title'] || nextMeta['og:title'] || title);
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
    }


    setBreadcrumbTitles(value) {
        this._routeTitle = value;
    }

    setTitle(value) {
        if (process.env.IS_SSR) {
            _set(this._ssrData, 'title', value);
        } else {
            document.title = value;
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
