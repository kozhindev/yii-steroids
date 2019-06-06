import {AUTH_INIT_USER} from '../actions/auth';

const initialState = {
    isInitialized: false,
    user: null,
};

export default (state = initialState, action) => {
    switch (action.type) {
        case AUTH_INIT_USER:
            return {
                ...state,
                isInitialized: true,
                user: action.user,
            };
    }

    return state;
};

export const isInitialized = state => state.auth.isInitialized;
export const isAuthorized = state => !!state.auth.user;
export const getUser = state => state.auth.user;
