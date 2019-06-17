import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _isArray from 'lodash-es/isArray';
import _isObject from 'lodash-es/isObject';
import _isEqual from 'lodash-es/isEqual';
import {getCurrentRoute} from '../../reducers/routing';
import {isInitialized} from '../../reducers/navigation';
import {initRoutes, initParams} from '../../actions/navigation';
import {store} from 'components';

const stateMap = state => ({
    isInitialized: isInitialized(state),
    route: getCurrentRoute(state),
});

export default routes => WrappedComponent => @connect(stateMap)
class NavigationHoc extends React.Component {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;

    static propTypes = {
        isInitialized: PropTypes.bool,
    };

    constructor() {
        super(...arguments);

        this._walkRoutesRecursive = this._walkRoutesRecursive.bind(this);
    }

    componentWillMount() {
        const routesTree = routes || (!_isArray(this.props.routes) ? this.props.routes : null);
        if (routesTree) {
            store.dispatch(initRoutes(this._walkRoutesRecursive({id: 'root', ...routesTree})));
        }

        this._initParams(this.props);
    }

    componentWillReceiveProps(nextProps, nextContext) {
        if (!this.props.route && nextProps.route) {
            this._initParams(nextProps);
        }
    }

    render() {
        if (!_isArray(this.props.routes) && !this.props.isInitialized) {
            return null;
        }

        return (
            <WrappedComponent {...this.props}/>
        );
    }

    _initParams(props) {
        if (props.route) {
            store.dispatch(initParams(props.route.params));
        }
    }

    _walkRoutesRecursive(item) {
        let items = null;
        if (_isArray(item.items)) {
            items = item.items.map(this._walkRoutesRecursive);
        } else if (_isObject(item.items)) {
            items = Object.keys(item.items).map(id => this._walkRoutesRecursive({
                ...item.items[id],
                id,
            }));
        }
        return {
            ...item,
            id: item.id,
            exact: item.exact,
            path: item.path,
            label: item.label,
            title: item.title,
            isVisible: item.isVisible,
            component: null,
            componentProps: null,
            items,
        };
    }

};
