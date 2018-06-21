import React from 'react';
import PropTypes from 'prop-types';
import {Route} from 'react-router';
import {ConnectedRouter} from 'react-router-redux';

import {store} from 'components';

export default class Router extends React.PureComponent {

    static propTypes = {
        routes: PropTypes.arrayOf(PropTypes.shape({
            path: PropTypes.string,
            component: PropTypes.func,
        })),
    };

    constructor() {
        super(...arguments);

        this._renderItem = this._renderItem.bind(this);
    }

    render() {
        return (
            <ConnectedRouter
                history={store.history}
            >
                <div>
                    {this.props.routes.map((route, index) => (
                        <Route
                            key={index}
                            render={props => this._renderItem(route, props)}
                            {...route}
                            component={null}
                        />
                    ))}
                </div>
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