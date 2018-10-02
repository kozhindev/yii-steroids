import queryString from 'query-string';
import _isArray from 'lodash-es/isArray';

export default class ResourceComponent {

    static RESOURCE_GOOGLE_MAP_API = '//maps.googleapis.com/maps/api/js';
    static RESOURCE_YANDEX_MAP_API = 'https://api-maps.yandex.ru/2.1/';
    static RESOURCE_TWITTER_WIDGET = 'https://platform.twitter.com/widgets.js';
    static RESOURCE_GEETEST_API = '//static.geetest.com/static/tools/gt.js';

    constructor() {
        this.googleApiKey = '';
        this.googleCaptchaSiteKey = '';

        this._callbacks = {};
    }

    loadGoogleMapApi() {
        const locale = require('components').locale;

        if (window.google && window.google.maps) {
            return Promise.resolve(window.google.maps);
        }

        return this._loadScript(
            ResourceComponent.RESOURCE_GOOGLE_MAP_API,
            {
                libraries: 'places',
                key: this.googleApiKey,
                language: locale.language,
            },
            () => new Promise(resolve => resolve(window.google.maps))
        );
    }

    loadYandexMap() {
        const locale = require('components').locale;

        if (window.ymaps) {
            return new Promise(resolve => window.ymaps.ready(() => resolve(window.ymaps)));
        }

        return this._loadScript(
            ResourceComponent.RESOURCE_YANDEX_MAP_API,
            {
                lang: locale.language,
            },
            () => new Promise(resolve => window.ymaps.ready(() => resolve(window.ymaps)))
        );
    }

    loadTwitterWidget() {
        if (window.twttr) {
            return new Promise(resolve => resolve(window.twttr));
        }

        return this._loadScript(
            ResourceComponent.RESOURCE_TWITTER_WIDGET,
            {},
            () => new Promise(resolve => window.twttr.ready(() => resolve(window.twttr)))
        );
    }

    loadGeetest() {
        if (window.initGeetest) {
            return new Promise(resolve => resolve(window.initGeetest));
        }
        return this._loadScript(
            ResourceComponent.RESOURCE_GEETEST_API + '?_t=' + (new Date()).getTime(),
            {},
            () => new Promise(resolve => resolve(window.initGeetest))
        );
    }

    _loadScript(url, params, firstResolver) {
        if (this._callbacks[url] === true) {
            return Promise.resolve();
        }

        if (_isArray(this._callbacks[url])) {
            return new Promise(resolve => {
                this._callbacks[url].push(resolve);
            });
        }

        this._callbacks[url] = [];

        // Append script to page
        return new Promise((resolve, reject) => {
            let script = document.createElement('script');
            script.async = true;
            script.onload = () => {
                firstResolver()
                    .then(result => {
                        // Resolve current
                        resolve(result);

                        // Resolve queue promises after current
                        const callbacks = this._callbacks[url];
                        this._callbacks[url] = true;
                        callbacks.forEach(callback => callback(result));
                    })
                    .catch(reject);
            };
            script.src = url + (params ? '?' + queryString.stringify(params) : '');
            document.body.appendChild(script);
        });
    }

}
