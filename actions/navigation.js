import _isArray from 'lodash-es/isArray';
import _trim from 'lodash-es/trim';
import {push} from 'react-router-redux';

import {http} from 'components';

export const NAVIGATION_INIT_ROUTES = 'NAVIGATION_INIT_ROUTES';
export const NAVIGATION_SET_PARAMS = 'NAVIGATION_SET_PARAMS';
export const NAVIGATION_ADD_CONFIGS = 'NAVIGATION_ADD_CONFIGS';
export const NAVIGATION_REMOVE_CONFIGS = 'NAVIGATION_REMOVE_CONFIGS';
export const NAVIGATION_SET_DATA = 'NAVIGATION_SET_DATA';

const normalizeConfigs = configs => {
    if (!configs) {
        configs = [];
    }
    if (!_isArray(configs)) {
        configs = [configs];
    }

    configs.forEach((config, index) => {
        if (!config.key || !config.url) {
            throw new Error('key and url is required');
        }

        configs[index] = {
            method: 'get',
            params: {},
            ...config,
        };
    });

    return configs;
};

const fetch = config => http.send(config.method, config.url, config.params).then(result => result.data);

export const initRoutes = routesTree => ({
    type: NAVIGATION_INIT_ROUTES,
    routesTree,
});

export const initParams = params => ({
    type: NAVIGATION_SET_PARAMS,
    params,
});

export const goToPage = (pageId, params) => (dispatch, getState) => {
    const getNavUrl = require('../reducers/navigation').getNavUrl;
    return dispatch(push(getNavUrl(getState(), pageId, params)));
};

export const getConfigId = config => config.id || _trim(config.url, '/');

export const navigationAddConfigs = configs => dispatch => {
    configs = normalizeConfigs(configs);

    dispatch({
        type: NAVIGATION_ADD_CONFIGS,
        configs,
    });

    configs.forEach(config => {
        fetch(config)
            .then(data => dispatch({
                type: NAVIGATION_SET_DATA,
                config,
                data,
            }));
    });
};

export const navigationRemoveConfigs = configs => {
    configs = normalizeConfigs(configs);

    return {
        type: NAVIGATION_REMOVE_CONFIGS,
        configs,
    };
};
