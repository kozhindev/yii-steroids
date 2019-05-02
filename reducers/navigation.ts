//TODO: replace "any" and "object"

import pathToRegexp from 'path-to-regexp';
import {matchPath} from 'react-router';
import _get from 'lodash-es/get';

import { NAVIGATION_INIT_ROUTES, NAVIGATION_SET_PARAMS } from '../actions/actionTypes';
import { IntInitParamsl, IntInitRoutes} from '../actions/navigation.d';
import {navigationState} from '../state/initialState';
import RootStateModel from '../models/RootState';
import RoutesTreeItemModel from './../models/RoutesTreeItem';
import DynamicObjectModel from '../models/DynamicObject';
import NavItemModel from '../models/NavItem';
import {getCurrentRoute} from './routing';

type TNavigationAction = IntInitParamsl | IntInitRoutes;
type TStringOrNull = string | null;
type TRouteOrNull = RoutesTreeItemModel | null;
type TNavItemOrNull = NavItemModel | null;
type TArrayOfRoutesTreeItemOrUndefined = Array<RoutesTreeItemModel> | undefined;

export default (state = navigationState, action: TNavigationAction) => {
    switch (action.type) {
        case NAVIGATION_INIT_ROUTES:
            return {
                ...state,
                routesTree: action.routesTree,
            };

        case NAVIGATION_SET_PARAMS:
            return {
                ...state,
                params: {
                    ...state.params,
                    ...action.params,
                },
            };
    }

    return state;
};

const findRecursive = (items: TArrayOfRoutesTreeItemOrUndefined, pageId: TStringOrNull, pathItems?: Array<any>): TRouteOrNull => {
    let finedItem: TRouteOrNull = null;

    (items || []).forEach(item => {
        if (item.id === pageId) {
            finedItem = item;
        }
        if (!finedItem) {
            finedItem = findRecursive(item.items, pageId, pathItems);
            if (finedItem && pathItems) {
                pathItems.push(item);
            }
        }
    });

    return finedItem;
};

const checkActiveRecursive = (pathname: string, item: RoutesTreeItemModel): boolean => {
    const match = matchPath(pathname, {
        exact: !!item.exact,
        strict: !!item.strict,
        path: item.path,
    });
    if (!match) {
        return !!(item.items || []).find(sub => checkActiveRecursive(pathname, sub));
    }
    return true;
};

const buildNavItem = (state: RootStateModel, item: RoutesTreeItemModel, params: DynamicObjectModel<any>): NavItemModel => {
    const pathname: string = _get(state, 'routing.location.pathname');
    let url: string = item.path;
    try {
        url = pathToRegexp.compile(item.path)({
            ...state.navigation.params,
            ...params,
        });
    } catch (e) { // eslint-disable-line no-empty
    }

    return {
        id: item.id,
        title: item.title,
        label: item.label,
        url: url,
        icon: item.icon || null, // you can set icon property to route in routes tree
        isVisible: item.isVisible,
        isActive: checkActiveRecursive(pathname, item),
    };
};

export const isInitialized = (state: RootStateModel): boolean => !!state.navigation.routesTree;

export const getBreadcrumbs = (state: RootStateModel, pageId: TStringOrNull = null, params: DynamicObjectModel<any> = {}): Array<NavItemModel> => {
    const items: Array<RoutesTreeItemModel> = [];
    const root: TRouteOrNull = state.navigation.routesTree;
    if (root) {
        if (root.id !== pageId) {
            const route = findRecursive(root.items, pageId, items);
            items.push(root);
            items.reverse();

            if (route) {
                items.push(route);
            }
        } else {
            items.push(root);
        }
    }

    return items.filter(item => item.isVisible !== false).map(route => buildNavItem(state, route, params));
};

export const getNavItem = (state: RootStateModel, pageId: string, params: DynamicObjectModel<any> = {}): TNavItemOrNull => {
    const route = getRoute(state, pageId);
    return route ? buildNavItem(state, route, params) : null;
};

export const getNavUrl = (state: RootStateModel, pageId: string, params: DynamicObjectModel<any> = {}): string => {
    const navItem = getNavItem(state, pageId, params);
    return navItem ? navItem.url : '';
};

export const getRoute = (state: RootStateModel, pageId: string): TRouteOrNull => {
    const root: TRouteOrNull = state.navigation.routesTree;
    if (!root) {
        return null;
    }

    return root.id === pageId ? root : findRecursive(root.items, pageId);
};

export const getCurrentItem = (state: RootStateModel): TRouteOrNull => {
    const route = getCurrentRoute(state);
    return route ? getRoute(state, route.id) : null;
};

export const getCurrentItemParam = (state: RootStateModel, param: string): any | null => {
    const item: any = getCurrentItem(state);
    return item ? item[param] : null;
};

export const getNavItems = (state: RootStateModel, parentPageId: TStringOrNull = null, params: DynamicObjectModel<any> = {}): Array<NavItemModel> => {
    const route: TRouteOrNull = getRoute(state, parentPageId as string); //TODO: find better solution "as string"

    return route
        ? (route.items || []).filter(item => item.isVisible !== false).map(item => buildNavItem(state, item, params))
        : [];
};

