import React from 'react';
import PropTypes from 'prop-types';
import {Route, Switch} from 'react-router';
import {connect} from 'react-redux';
import {ConnectedRouter} from 'react-router-redux';
import _get from 'lodash-es/get';
import _isArray from 'lodash-es/isArray';
import _isObject from 'lodash-es/isObject';

import {store} from 'components';
import {registerRoutes} from '../../../actions/routing';
import navigationHoc from 'yii-steroids/ui/nav/navigationHoc';

export default
@navigationHoc()
@connect(
    state => ({
        pathname: _get(state, 'routing.location.pathname'),
    })
)
class Router extends React.PureComponent {

    static propTypes = {
        wrapperView: PropTypes.func,
        wrapperProps: PropTypes.object,
        routes: PropTypes.oneOfType([
            PropTypes.object,
            PropTypes.arrayOf(PropTypes.shape({
                path: PropTypes.string,
                component: PropTypes.func,
            })),
        ]),
        pathname: PropTypes.string,
    };

    static treeToList(item) {
        let items = [item];
        if (_isArray(item.items)) {
            item.items.forEach(sub => {
                items = items.concat(Application.treeToList(sub));
            });
        }
        if (_isObject(item.items)) {
            Object.keys(item.items).map(id => {
                items.push({
                    ...item.items[id],
                    id,
                });
            });
        }

        return items;
    }

    constructor() {
        super(...arguments);

        this._routes = _isObject(this.props.routes) ? Router.treeToList(this.props.routes) : this.props.routes;
        this._renderItem = this._renderItem.bind(this);
    }

    componentWillMount() {
        this.props.dispatch(registerRoutes(this._routes));
    }

    componentWillReceiveProps(nextProps) {
        // Fix end slash on switch to base route
        if (window.history && nextProps.pathname === '/' && location.pathname.match(/\/$/)) {
            window.history.replaceState({}, '', store.history.basename);
        }
    }

    render() {
        const WrapperComponent = this.props.wrapperView;
        const routes = (
            <Switch>
                {this._routes.map((route, index) => (
                    <Route
                        key={index}
                        render={props => this._renderItem(route, props)}
                        {...route}
                        component={null}
                    />
                ))}
                {this.props.children}
            </Switch>
        );

        return (
            <ConnectedRouter history={store.history}>
                {WrapperComponent && (
                    <WrapperComponent {...this.props.wrapperProps}>
                        {routes}
                    </WrapperComponent>
                )
                || (
                    routes
                )}
            </ConnectedRouter>
        );
    }

    _renderItem(route, props) {
        const Component = route.component;

        return (
            <Component
                {...props}
                {...route.componentProps}
            />
        );
    }

}
