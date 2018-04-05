import React from 'react';
import _trimStart from 'lodash-es/trimStart';
import _trimEnd from 'lodash-es/trimEnd';
// TODO import {setFlashes} from 'actions/notifications';
import axios from 'axios';

export default class HttpComponent {

    constructor(store) {
        this.apiUrl = '//' + location.host;
        this.store = store;
        this._lazyRequests = {};

        axios.interceptors.request.use((config) => {
            // Add CSRF header
            const metaToken = document.querySelector('meta[name=csrf-token]');
            if (metaToken) {
                config.headers['X-CSRF-Token'] = metaToken.getAttribute('content');
            }

            // Add XMLHttpRequest header for detect ajax requests
            config.headers['X-Requested-With'] = 'XMLHttpRequest';

            return config;
        });
    }

    get(method, params = {}, options = {}) {
        return this._send(method, {
            method: 'get',
            params: params,
        }, options);
    }

    post(method, params = {}, options = {}) {
        return this._send(method, {
            method: 'post',
            data: params,
        }, options);
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
            url: `${_trimEnd(this.apiUrl, '/')}/${_trimStart(method, '/')}`,
            withCredentials: true,
            headers: {
                'Content-Type': 'application/json',
            },
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
        return axios(config)
            .then(response => response.data)
            .then(response => {
                // Flash
                if (response.flashes) {
                    //this.store.dispatch(setFlashes(response.flashes));
                }

                // Ajax redirect
                if (response.redirectUrl) {
                    if (location.href === response.redirectUrl.split('#')[0]) {
                        window.location.href = response.redirectUrl;
                        window.location.reload();
                    } else {
                        window.location.href = response.redirectUrl;
                    }
                }

                return response;
            });
    }

}
