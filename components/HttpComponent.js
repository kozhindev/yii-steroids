import React from 'react';
import _trimStart from 'lodash-es/trimStart';
import _trimEnd from 'lodash-es/trimEnd';
import {setFlashes} from '../actions/notifications';
import axios from 'axios';
import _isFunction from 'lodash-es/isFunction';
import _isObject from 'lodash-es/isObject';

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

    resetConfig() {
        this._axios = null;
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

                this._isRendered = false;
                this._cancels = [];
                this._fetch = this._fetch.bind(this);
                this._createCancelToken = this._createCancelToken.bind(this);
            }

            componentDidMount() {
                this._isRendered = true;
                this._fetch();
            }

            componentWillUnmount() {
                this._isRendered = false;
                this._cancels.forEach(cancel => cancel('Canceled on unmount component'));
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

            _createCancelToken() {
                return new axios.CancelToken(cancel => {
                    this._cancels.push(cancel);
                });
            }

            _fetch(params) {
                const result = requestFunc({
                    ...this.props,
                    ...params,
                    createCancelToken: this._createCancelToken,
                });

                if (_isObject(result)) {
                    if (_isFunction(result.then)) {
                        return result.then(data => {
                            if (this._isRendered) {
                                this.setState({data});
                            }
                            return data;
                        });
                    } else {
                        this.setState({data: result});
                    }
                }

                return result;
            }

        };
    }

    _send(method, config, options) {
        if (method === null) {
            method = location.pathname;
        }
        if (method.indexOf('://') === -1) {
            method = `${_trimEnd(this.apiUrl, '/')}/${_trimStart(method, '/')}`;
        }

        const axiosConfig = {
            ...config,
            url: method,
        };

        if (options.cancelToken) {
            axiosConfig.cancelToken = options.cancelToken;
        }

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
