import { TYPE_NAVIGATION_INIT_ROUTES, TYPE_NAVIGATION_SET_PARAMS } from './actionTypes.d';
import RoutesTreeItemModel from './../models/RoutesTreeItem';

//TODO: replace "any" and "object"
export interface IntInitRoutes {
    type: TYPE_NAVIGATION_INIT_ROUTES;
    routesTree: RoutesTreeItemModel;
}

export interface IntInitParamsl {
    type: TYPE_NAVIGATION_SET_PARAMS;
    params: object;
}