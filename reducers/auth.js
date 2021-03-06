import _merge from 'lodash-es/merge';
import _omit from 'lodash-es/omit';
import {
    AUTH_INIT_USER,
    AUTH_SET_DATA,
    AUTH_ADD_SOCIAL,
    AUTH_REMOVE_DATA_KEYS,
} from '../actions/auth';

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
                data: _merge(state.data, action.data),
            };
        case AUTH_ADD_SOCIAL:
            return {
                ...state,
                user: {
                    ...state.user,
                    socials: [
                        ...state.user.socials,
                        action.social,
                    ],
                }
            };

        case AUTH_REMOVE_DATA_KEYS:
            return {
                ...state,
                data: {
                    ..._omit(state.data, action.keys),
                }
            };
    }

    return state;
};

export const isInitialized = state => state.auth.isInitialized;
export const isAuthorized = state => !!state.auth.user;
export const getUser = state => state.auth.user;
export const getUserId = state => state.auth.user && state.auth.user.id || null;
export const getUserRole = state => state.auth.user && state.auth.user.role || null;
export const getData = state => state.auth.data;
