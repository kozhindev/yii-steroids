import {NAVIGATION_INIT_ROUTES, NAVIGATION_SET_PARAMS} from '../actions/navigation';


const initialState = {
    routesTree: null,
    params: {},
};

export default (state = initialState, action) => {
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

export const isInitialized = state => !!state.navigation.routesTree;