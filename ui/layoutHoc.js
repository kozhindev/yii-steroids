import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash-es/get';
import _upperFirst from 'lodash-es/upperFirst';
import _isFunction from 'lodash-es/isFunction';
import _isObject from 'lodash-es/isObject';
import _merge from 'lodash-es/merge';
import {getCurrentItem} from 'yii-steroids/reducers/navigation';

import * as components from 'components';
import {store} from 'components';
import {getUser} from '../reducers/auth';
import {setUser} from '../actions/auth';
import {setMeta} from '../actions/fields';

const stateMap = state => ({
    page: getCurrentItem(state),
    user: getUser(state),
});

export const STATUS_LOADING = 'loading';
export const STATUS_NOT_FOUND = 'not_found';
export const STATUS_RENDER_ERROR = 'render_error';
export const STATUS_HTTP_ERROR = 'render_error';
export const STATUS_ACCESS_DENIED = 'access_denied';
export const STATUS_OK = 'ok';

export default (initAction) => WrappedComponent => @connect(stateMap)
    class LayoutHoc extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;

    static propTypes = {
        page: PropTypes.shape({
            id: PropTypes.string,
            roles: PropTypes.arrayOf(PropTypes.string),
        }),
        user: PropTypes.shape({
            role: PropTypes.string,
        }),
    };

    static getDerivedStateFromError(e) {
        return {
            renderError: String(e),
        };
    }

    constructor() {
        super(...arguments);

        this.state = {
            isLoading: true,
            renderError: null,
            httpError: null,
            data: null,
        };
    }

    componentWillMount() {
        // Callback for load initial page data (return promise)
        if (_isFunction(initAction)) {
            initAction(this.props)
                .then(data => {
                    // Configure components
                    if (_isObject(data.config)) {
                        Object.keys(data.config).map(name => {
                            if (components[name]) {
                                Object.keys(data.config[name]).map(key => {
                                    const value = data.config[name][key];
                                    const setter = 'set' + _upperFirst(key);
                                    if (_isFunction(components[name][setter])) {
                                        components[name][setter](value);
                                    } else if (_isObject(components[name][key]) && _isObject(value)) {
                                        _merge(components[name][key], value);
                                    } else {
                                        components[name][key] = value;
                                    }
                                });
                            }
                        });
                    }

                    store.dispatch([
                        // User auth
                        setUser(data.user),

                        // Meta models & enums
                        data.meta && setMeta(data.meta),
                    ].filter(Boolean));

                    this.setState({
                        isLoading: false,
                        data,
                    });
                })
                .catch(e => {
                    this.setState({
                        httpError: e,
                    });

                    throw e;
                })
        } else {
            this.setState({
                isLoading: false,
            });
        }
    }

    render() {
        let status = STATUS_OK;
        if (this.state.isLoading) {
            status = STATUS_LOADING;
        } else if (this.state.renderError) {
            status = STATUS_RENDER_ERROR;
        } else if (this.state.httpError) {
            status = STATUS_HTTP_ERROR;
        } else if (!this.props.page) {
            status = STATUS_NOT_FOUND;
        } else {
            const pageRoles = _get(this.props, 'page.roles') || [];
            const userRole = _get(this.props, 'user.role') || null;

            if (!pageRoles.includes(userRole)) {
                status = STATUS_ACCESS_DENIED;

                if (process.env.NODE_ENV !== 'production') {
                    console.log('Access denied. Page roles: ', pageRoles, 'User role:', userRole, 'Page:', this.props.page);
                }
            }
        }

        return (
            <WrappedComponent
                {...this.props}
                {...this.state.data}
                status={status}
            />
        );
    }
};

