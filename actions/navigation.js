export const NAVIGATION_INIT_ROUTES = 'NAVIGATION_INIT_ROUTES';
export const NAVIGATION_SET_PARAMS = 'NAVIGATION_SET_PARAMS';

export const initRoutes = routesTree => ({
    type: NAVIGATION_INIT_ROUTES,
    routesTree,
});

export const initParams = params => ({
    type: NAVIGATION_SET_PARAMS,
    params,
});
