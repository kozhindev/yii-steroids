import {AUTH_INIT_USER, AUTH_SET_DATA} from '../actions/auth';

const initialState = {
    isInitialized: false,
    user: null,
    data: null,
};

export default (state = initialState, action) => {
    switch (action.type) {
        case AUTH_INIT_USER:
            return {
                ...state,
                isInitialized: true,
                user: action.user,
            };
        case AUTH_SET_DATA:
            return {
                ...state,
                isInitialized: true,
                data: action.data,
            };
    }

    return state;
};

export const isInitialized = state => state.auth.isInitialized;
export const isAuthorized = state => !!state.auth.user;
export const getUser = state => state.auth.user;
export const getData = state => state.auth.data;
