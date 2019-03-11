import React from 'react';
import PropTypes from 'prop-types';
import {Route, Switch} from 'react-router';
import {connect} from 'react-redux';
import {ConnectedRouter} from 'react-router-redux';
import _get from 'lodash-es/get';

import {store} from 'components';
import {registerRoutes} from '../../../actions/routing';

export default
@connect(
    state => ({
        pathname: _get(state, 'routing.location.pathname'),
    })
)
class Router extends React.PureComponent {

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

    componentWillMount() {
        this.props.dispatch(registerRoutes(this.props.routes));
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
                {this.props.routes.map((route, index) => (
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