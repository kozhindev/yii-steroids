import {createStore, applyMiddleware, compose} from 'redux';
import {routerMiddleware} from 'react-router-redux';
import createHistory from 'history/createBrowserHistory';
import _get from 'lodash-es/get';
import _merge from 'lodash-es/merge';
import _isPlainObject from 'lodash-es/isPlainObject';

import reducers from 'reducers';

export default class StoreComponent {

    constructor() {
        const initialState = _merge(...(window.APP_REDUX_PRELOAD_STATES || [{}]));

        this.history = createHistory(_get(initialState, 'config.store.history', {}));
        this.store = createStore(
            reducers,
            initialState,
            compose(
                applyMiddleware(({getState}) => next => action => this._prepare(action, next, getState)),
                applyMiddleware(routerMiddleware(this.history)),
                window.__REDUX_DEVTOOLS_EXTENSION__ ? window.__REDUX_DEVTOOLS_EXTENSION__() : f => f
            )
        );
    }

    dispatch(action) {
        return this.store.dispatch(action);
    }

    getState() {
        return this.store.getState();
    }

    errorHandler(error) {
        throw error;
    }

    _prepare(action, dispatch, getState) {
        // Multiple dispatch (redux-multi)
        if (Array.isArray(action)) {
            return action.filter(v => v).map(p => this._prepare(p, dispatch, getState));
        }

        // Function wraper (redux-thunk)
        if (typeof action === 'function') {
            return action(p => this._prepare(p, dispatch, getState), getState);
        }

        // Promise, detect errors on rejects
        // Detect action through instanceof Promise is not working in production mode, then used single detection by type
        if (typeof action === 'object' && typeof action.then === 'function' && typeof action.catch === 'function') {
            return action
                .then(payload => this._prepare(payload, dispatch, getState))
                .catch(e => {
                    this.errorHandler(e, p => this._prepare(p, dispatch, getState));
                });
        }

        // Default case
        if (_isPlainObject(action) && action.type) {
            if (process.env.NODE_ENV !== 'production') {
                window.__snapshot = (window.__snapshot || []).concat({action});
            }

            try {
                return dispatch(action);
            } catch (e) {
                this.errorHandler(e, p => this._prepare(p, dispatch, getState));
            }
        }

        return action;
    }

}
