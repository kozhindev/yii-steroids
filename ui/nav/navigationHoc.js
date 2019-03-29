import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash-es/get';
import _isArray from 'lodash-es/isArray';
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
            store.dispatch([
                initRoutes(this._walkRoutesRecursive(routes)),
                initParams(_get(this.props, 'route.params')),
            ]);
        }

        render() {
            if (!this.props.isInitialized) {
                return null;
            }

            return (
                <WrappedComponent {...this.props}/>
            );
        }

        _walkRoutesRecursive(item) {
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
                items: _isArray(item.items)
                    ? item.items.map(this._walkRoutesRecursive)
                    : null,
            };
        }

};
