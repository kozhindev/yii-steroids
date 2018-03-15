import {createStore, applyMiddleware} from 'redux';
import _merge from 'lodash/merge';
import _isPlainObject from 'lodash/isPlainObject';

import reducer from 'reducers';

export default class StoreComponent {

    constructor() {
        this.store = createStore(
            reducer,
            _merge(...(window.APP_REDUX_PRELOAD_STATES || [{}])),
            applyMiddleware(this._middleware)
        );
    }

    errorHandler(error) {
        throw error;
    }

    _middleware({getState}) {
        return next => action => this._prepare(action, next, getState)
    }

    _prepare() {
        return (action, dispatch, getState) => {
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
        };
    }

}
