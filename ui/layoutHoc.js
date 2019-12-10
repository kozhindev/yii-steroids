import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash-es/get';
import _isFunction from 'lodash-es/isFunction';
import {getCurrentItemParam} from 'yii-steroids/reducers/navigation';

import {getData, getUser, isInitialized} from '../reducers/auth';
import {init} from '../actions/auth';
import {getBreadcrumbs} from '../reducers/navigation';

import {head} from 'components';

export const STATUS_LOADING = 'loading';
export const STATUS_NOT_FOUND = 'not_found';
export const STATUS_RENDER_ERROR = 'render_error';
export const STATUS_HTTP_ERROR = 'render_error';
export const STATUS_ACCESS_DENIED = 'access_denied';
export const STATUS_OK = 'ok';

const stateMap = state => {
    const pageId = getCurrentItemParam(state, 'id');
    const breadcrumbs = getBreadcrumbs(state, pageId);
    const page = breadcrumbs.pop();
    const breadcrumbItems = breadcrumbs.filter(item => item.isDocumentTitleVisible !== false);

    return {
        page,
        user: getUser(state),
        data: getData(state),
        isInitialized: isInitialized(state),
        routeTitle: page && page.isDocumentTitleVisible !== false && (page.title || page.label) || null,
        routeBreadcrumbTitles: breadcrumbItems.reverse().map(item => item.title || item.label).filter(Boolean).join(' | '),
    };
};

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
        routeTitle: PropTypes.string,
        routeBreadcrumbTitles: PropTypes.string,
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

    UNSAFE_componentWillMount() {
        // Callback for load initial page data (return promise)
        if (_isFunction(initAction)) {
            this.props.dispatch(init(initAction, true))
                .catch(e => {
                    this.setState({
                        httpError: e,
                    });

                    throw e;
                });
        }

        if (this.props.routeTitle) {
            head.setRouteTitle(this.props.routeTitle);
        }
        if (this.props.routeBreadcrumbTitles) {
            head.setBreadcrumbTitles(this.props.routeBreadcrumbTitles.split(' | '));
        }
    }

    componentDidUpdate(prevProps) {
        if (prevProps.routeTitle !== this.props.routeTitle) {
            head.setRouteTitle(this.props.routeTitle);
        }
        if (prevProps.routeBreadcrumbTitles !== this.props.routeBreadcrumbTitles) {
            head.setBreadcrumbTitles(this.props.routeBreadcrumbTitles.split(' | '));
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

