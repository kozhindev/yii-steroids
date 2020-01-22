import _isFunction from 'lodash-es/isFunction';
import _isObject from 'lodash-es/isObject';
import _upperFirst from 'lodash-es/upperFirst';
import _merge from 'lodash-es/merge';

import {http} from 'components';
import {setMeta} from './fields';
import {goToPage} from './navigation';

export const AUTH_INIT_USER = 'AUTH_INIT_USER';
export const AUTH_SET_DATA = 'AUTH_SET_DATA';
export const AUTH_ADD_SOCIAL = 'AUTH_ADD_SOCIAL';

let lastInitAction = null;

export const init = (initAction, skipInitialized = false) => (dispatch, getState) => {
    lastInitAction = initAction;

    const state = getState();
    if (skipInitialized && state.auth && state.auth.isInitialized) {
        return Promise.resolve([]);
    }

    return initAction(state)
        .then(data => {
            // Configure components
            if (_isObject(data.config)) {
                const components = require('components');
                Object.keys(data.config).map(name => {
                    if (components[name]) {
                        Object.keys(data.config[name]).map(key => {
                            const value = data.config[name][key];
                            const setter = 'set' + _upperFirst(key);
                            if (_isFunction(components[name][setter])) {
                                components[name][setter](value);
                            } else if (_isObject(components[name][key]) && _isObject(value)) {
                                _merge(components[name][key], value);
                            } else {
                                components[name][key] = value;
                            }
                        });
                    }
                });
            }

            return dispatch([
                // User auth
                setUser(data.user),

                // Meta models & enums
                data.meta && setMeta(data.meta),

                // User auth
                setData(data),
            ].filter(Boolean));
        });
};

export const reInit = () => init(lastInitAction);

export const login = (token, redirectRouteId = 'root', redirectRouteParams) => dispatch => {
    http.setAccessToken(token);
    return dispatch(init(lastInitAction))
        .then(() => dispatch(goToPage(redirectRouteId, redirectRouteParams)));
};

export const addSocial = social => ({
    type: AUTH_ADD_SOCIAL,
    social,
});

export const setUser = user => ({
    type: AUTH_INIT_USER,
    user: user || null,
});

export const setData = data => ({
    type: AUTH_SET_DATA,
    data,
});

export const logout = () => dispatch => {
    http.setAccessToken(null);
    return dispatch([
        setUser(null),
        goToPage('root')
    ]);
};
