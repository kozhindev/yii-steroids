import React from 'react';
import PropTypes from 'prop-types';
import {Route, Switch, StaticRouter} from 'react-router';
import {connect} from 'react-redux';
import {ConnectedRouter} from 'connected-react-router';
import _get from 'lodash-es/get';

import {store} from 'components';
import {registerRoutes} from '../../../actions/routing';
import navigationHoc, {treeToList} from '../navigationHoc';
import fetchHoc from '../fetchHoc';

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

    static contextTypes = {
        history: PropTypes.object,
    };

    constructor() {
        super(...arguments);

        this._renderItem = this._renderItem.bind(this);

        this.state = {
            routes: treeToList(this.props.routes),
        };
    }

    UNSAFE_componentWillMount() {
        this.props.dispatch(registerRoutes(this.state.routes));
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (this.props.routes !== nextProps.routes) {
            this.setState({routes: nextProps.routes});
        }

        // Fix end slash on switch to base route
        if (window.history && nextProps.pathname === '/' && location.pathname.match(/\/$/)) {
            window.history.replaceState({}, '', store.history.basename);
        }
    }

    render() {
        // TODO double render!!..

        if (process.env.IS_SSR) {
            return (
                <StaticRouter
                    location={this.context.history.location}
                    context={this.context.staticContext}
                >
                    {this.renderContent()}
                </StaticRouter>
            );
        } else {
            return (
                <ConnectedRouter history={store.history}>
                    {this.renderContent()}
                </ConnectedRouter>
            );
        }
    }

    renderContent() {
        const WrapperComponent = this.props.wrapperView;
        const routes = (
            <Switch>
                {this.state.routes.map((route, index) => (
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

        if (WrapperComponent) {
            return (
                <WrapperComponent {...this.props.wrapperProps}>
                    {routes}
                </WrapperComponent>
            );
        }

        return routes;
    }

    _renderItem(route, props) {
        let Component = route.component;

        if (route.fetch) {
            Component = fetchHoc(route.fetch)(Component);
        }

        return (
            <Component
                {...props}
                {...route.componentProps}
            />
        );
    }

}
