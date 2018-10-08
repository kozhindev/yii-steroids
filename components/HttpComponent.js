import React from 'react';
import _trimStart from 'lodash-es/trimStart';
import _trimEnd from 'lodash-es/trimEnd';
import {setFlashes} from '../actions/notifications';
import axios from 'axios';

export default class HttpComponent {

    constructor() {
        this.apiUrl = '//' + location.host;
        this._lazyRequests = {};
        this._axios = null;
    }

    getAxiosConfig() {
        const config = {
            withCredentials: true,
            headers: {
                // Add XMLHttpRequest header for detect ajax requests
                'X-Requested-With': 'XMLHttpRequest',

                // Add Content-Type
                'Content-Type': 'application/json',
            }
        };

        // Add CSRF header
        const metaToken = document.querySelector('meta[name=csrf-token]');
        if (metaToken) {
            config.headers['X-CSRF-Token'] = metaToken.getAttribute('content');
        }

        return config;
    }

    getAxiosInstance() {
        if (!this._axios) {
            this._axios = axios.create(this.getAxiosConfig());
        }
        return this._axios;
    }

    get(url, params = {}, options = {}) {
        return this._send(url, {
            method: 'get',
            params: params,
        }, options)
            .then(response => response.data);
    }

    post(url, params = {}, options = {}) {
        return this._send(url, {
            method: 'post',
            data: params,
        }, options)
            .then(response => response.data);
    }

    delete(url, params = {}, options = {}) {
        return this._send(url, {
            method: 'delete',
            data: params,
        }, options)
            .then(response => response.data);
    }

    send(method, url, params = {}, options = {}) {
        method = method.toLowerCase();

        return this._send(url, {
            method,
            [method === 'get' ? 'params' : 'data']: params,
        }, options, true);
    }

    hoc(requestFunc) {
        return WrappedComponent => class HttpHOC extends React.Component {

            static WrappedComponent = WrappedComponent;

            constructor() {
                super(...arguments);

                this.state = {
                    data: null,
                };

                this._fetch = this._fetch.bind(this);
            }

            componentDidMount() {
                this._fetch();
            }

            render() {
                return (
                    <WrappedComponent
                        {...this.props}
                        {...this.state.data}
                        fetch={this._fetch}
                    />
                );
            }

            _fetch(params) {
                return requestFunc({
                    ...this.props,
                    ...params,
                })
                    .then(data => {
                        this.setState({data});
                        return data;
                    });
            }

        };
    }

    _send(method, config, options) {
        const axiosConfig = {
            ...config,
            url: method !== null
                ? `${_trimEnd(this.apiUrl, '/')}/${_trimStart(method, '/')}`
                : location.pathname,
        };

        if (options.lazy) {
            if (this._lazyRequests[method]) {
                clearTimeout(this._lazyRequests[method]);
            }

            return new Promise((resolve, reject) => {
                const timeout = options.lazy !== true ? options.lazy : 200;
                this._lazyRequests[method] = setTimeout(() => {
                    this._sendAxios(axiosConfig)
                        .then(result => resolve(result))
                        .catch(result => reject(result));
                }, timeout);
            });
        }

        return this._sendAxios(axiosConfig);
    }

    _sendAxios(config) {
        return this.getAxiosInstance()(config)
            .then(response => {
                this.afterRequest(response);
                return response;
            });
    }

    afterRequest(response) {
        const store = require('components').store;

        // Flash
        if (response.data.flashes) {
            store.dispatch(setFlashes(response.data.flashes));
        }

        // Ajax redirect
        if (response.data.redirectUrl) {
            if (location.href === response.data.redirectUrl.split('#')[0]) {
                window.location.href = response.data.redirectUrl;
                window.location.reload();
            } else {
                window.location.href = response.data.redirectUrl;
            }
        }
    }

}
