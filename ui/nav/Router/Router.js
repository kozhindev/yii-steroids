import React from 'react';
import PropTypes from 'prop-types';
import {Route, Switch} from 'react-router';
import {connect} from 'react-redux';
import {ConnectedRouter} from 'react-router-redux';
import {matchPath} from 'react-router';
import _get from 'lodash-es/get';

import {store} from 'components';

@connect(
    state => ({
        pathname: _get(state, 'routing.location.pathname'),
    })
)
export default class Router extends React.PureComponent {

    static propTypes = {
        wrapperView: PropTypes.func,
        wrapperProps: PropTypes.object,
        routes: PropTypes.arrayOf(PropTypes.shape({
            path: PropTypes.string,
            component: PropTypes.func,
        })),
        pathname: PropTypes.string,
    };

    constructor() {
        super(...arguments);

        this._renderItem = this._renderItem.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        // Fix end slash on switch to base route
        if (window.history && nextProps.pathname === '/' && location.pathname.match(/\/$/)) {
            window.history.replaceState({}, '', store.history.basename);
        }
    }

    render() {
        // Find current route
        let currentRoute = null;
        this.props.routes.forEach(route => {
            const match = matchPath(this.props.pathname, route);
            if (match) {
                currentRoute = {
                    id: route.id,
                    ...match,
                };
            }
        });

        const WrapperComponent = this.props.wrapperView;
        const routes = (
            <Switch>
                {this.props.routes.map((route, index) => (
                    <Route
                        key={index}
                        render={props => this._renderItem(route, props)}
                        {...route}
                        component={null}
                    />
                ))}
            </Switch>
        );

        return (
            <ConnectedRouter
                history={store.history}
            >
                {WrapperComponent && (
                    <WrapperComponent
                        {...this.props.wrapperProps}
                        route={currentRoute}
                    >
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