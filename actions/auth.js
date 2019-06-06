import {http} from 'components';

export const AUTH_INIT_USER = 'AUTH_INIT_USER';

export const login = (token, user) => dispatch => {
    http.setAccessToken(token);
    return dispatch(setUser(user));
};

export const setUser = user => ({
    type: AUTH_INIT_USER,
    user: user || null,
});

export const logout = () => {
    http.setAccessToken(null);
    location.refresh();
};
