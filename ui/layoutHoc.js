import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash-es/get';
import _isFunction from 'lodash-es/isFunction';
import {getCurrentItem} from 'yii-steroids/reducers/navigation';

import {getData, getUser, isInitialized} from '../reducers/auth';
import {init} from '../actions/auth';

const stateMap = state => ({
    page: getCurrentItem(state),
    user: getUser(state),
    data: getData(state),
    isInitialized: isInitialized(state),
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
        data: PropTypes.object,
    };

    static getDerivedStateFromError(e) {
        return {
            renderError: String(e),
        };
    }

    constructor() {
        super(...arguments);

        this.state = {
            renderError: null,
            httpError: null,
        };
    }

    componentWillMount() {
        // Callback for load initial page data (return promise)
        if (_isFunction(initAction)) {
            this.props.dispatch(init(initAction))
                .catch(e => {
                    this.setState({
                        httpError: e,
                    });

                    throw e;
                })
        }
    }

    render() {
        let status = STATUS_OK;
        if (!this.props.isInitialized) {
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
                {...this.props.data}
                status={status}
            />
        );
    }
};

