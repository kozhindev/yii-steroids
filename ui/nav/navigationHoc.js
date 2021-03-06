import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _isArray from 'lodash-es/isArray';
import _isObject from 'lodash-es/isObject';
import {getCurrentRoute} from '../../reducers/navigation';
import {isInitialized} from '../../reducers/navigation';
import {initRoutes, initParams} from '../../actions/navigation';

const stateMap = state => ({
    isInitialized: isInitialized(state),
    route: getCurrentRoute(state),
});

export const walkRoutesRecursive = item => {
    let items = null;
    if (_isArray(item.items)) {
        items = item.items.map(walkRoutesRecursive);
    } else if (_isObject(item.items)) {
        items = Object.keys(item.items).map(id => walkRoutesRecursive({
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
};

export const treeToList = (item, isRoot = true) => {
    if (_isArray(item)) {
        return item;
    }

    if (isRoot && !item.id) {
        item.id = 'root';
    }

    let items = item.path ? [item] : [];
    if (_isArray(item.items)) {
        item.items.forEach(sub => {
            items = items.concat(treeToList(sub, false));
        });
    } else if (_isObject(item.items)) {
        Object.keys(item.items).map(id => {
            items = items.concat(treeToList({
                ...item.items[id],
                id,
            }, false));
        });
    }

    return items;
};

export default routes => WrappedComponent => @connect(stateMap)
class NavigationHoc extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;

    static propTypes = {
        isInitialized: PropTypes.bool,
    };

    UNSAFE_componentWillMount() {
        const routesTree = routes || (!_isArray(this.props.routes) ? this.props.routes : null);
        if (routesTree) {
            this.props.dispatch(initRoutes(walkRoutesRecursive({id: 'root', ...routesTree})));
        }

        this._initParams(this.props);
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
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
            this.props.dispatch(initParams(props.route.params));
        }
    }

};
