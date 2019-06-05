import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash-es/get';
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

    shouldComponentUpdate(nextProps) {
        return this.props.isInitialized !== nextProps.isInitialized
            || !_isEqual(this.props.route, nextProps.route);
    }

    componentWillMount() {
        const routesTree = routes || (_isObject(this.props.routes) ? this.props.routes : null);
        if (routesTree) {
            store.dispatch([
                initRoutes(this._walkRoutesRecursive(routesTree)),
                initParams(_get(this.props, 'route.params')),
            ]);
        }
    }

    render() {
        if (!this.props.isInitialized) {
            return null;
        }

        return (
            <WrappedComponent {...this.props}/>
        );
    }

    _walkRoutesRecursive(item, isRoot) {
        let items = null;
        if (_isArray(item.items)) {
            items = item.items.map(this._walkRoutesRecursive);
        }
        if (_isObject(item.items)) {
            items = Object.keys(item.items).map(id => this._walkRoutesRecursive({
                ...item.items[id],
                id,
            }));
        }
        return {
            ...item,
            id: item.id || (isRoot ? 'root' : ''),
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
