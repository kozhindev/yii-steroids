//TODO: replace "any" and "object"

import _get from 'lodash-es/get';
import { matchPath } from 'react-router';

import { LOCATION_CHANGE, ROUTING_REGISTER } from '../actions/actionTypes';
import { IntRegisterRoutes } from '../actions/routing.d';
import { routingState } from '../state/initialState';
import RootStateModel from '../models/RootState';
import RoutesTreeItemModel from '../models/RoutesTreeItem';
import RouteModel from '../models/Route';

type TRoutingAction = IntRegisterRoutes | any;

export default (state = routingState, action: TRoutingAction) => {
    switch (action.type) {
        case LOCATION_CHANGE:
            return {
                ...state,
                location: action.payload,
            };

        case ROUTING_REGISTER:
            return {
                ...state,
                routes: action.routes.map((item: RoutesTreeItemModel) => ({
                    id: item.id,
                    exact: item.exact,
                    strict: item.strict,
                    path: item.path,
                })),
            };
    }
    return state;
};

export const getCurrentRoute = (state: RootStateModel): RouteModel | null => {
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
