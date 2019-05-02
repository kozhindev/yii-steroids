import { NAVIGATION_INIT_ROUTES, NAVIGATION_SET_PARAMS } from './actionTypes';
import RoutesTreeItemModel from './../models/RoutesTreeItem';
import DynamicObjectModel from './../models/DynamicObject';

export const initRoutes = (routesTree: RoutesTreeItemModel) => ({
    type: NAVIGATION_INIT_ROUTES,
    routesTree,
});

export const initParams = (params: DynamicObjectModel<string>) => ({
    type: NAVIGATION_SET_PARAMS,
    params,
});
