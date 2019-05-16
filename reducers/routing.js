import _get from 'lodash-es/get';
import _isEmpty from 'lodash-es/isEmpty';
import {matchPath} from 'react-router';

import {ROUTING_REGISTER} from '../actions/routing';

export const LOCATION_CHANGE = '@@router/LOCATION_CHANGE';

const initialState = {
    location: null,
    action: null,
    routes: [],
};

export default (state = initialState, action) => {
    switch (action.type) {
        case LOCATION_CHANGE:
            return {
                ...state,
                location: action.payload,
            };

        case ROUTING_REGISTER:
            return {
                ...state,
                routes: action.routes.map(item => ({
                    id: item.id,
                    exact: item.exact,
                    strict: item.strict,
                    path: item.path,
                })),
            };
    }
    return state;
};


export const getCurrentRoute = (state) => {
    if (!state || _isEmpty(state)) {
        return null;
    }

    let currentRoute = null;
    const pathname = _get(state, 'routing.location.pathname');
    state.routing.routes.forEach(route => {
        const match = matchPath(pathname, route);
        if (match) {
            currentRoute = {
                id: route.id,
                ...match,
            };
        }
    });
    return currentRoute;
};
