let ID_COUNTER = 0;

export const SHOW_NOTIFICATION = 'SHOW_NOTIFICATION';
export const HIDE_NOTIFICATION = 'HIDE_NOTIFICATION';

export const showNotification = (message, level = 'warn') => dispatch => {
    const id = ++ID_COUNTER;
    dispatch({type: SHOW_NOTIFICATION, id, message, level});
    setTimeout(() => dispatch({type: HIDE_NOTIFICATION, id}), 10000);
};

export const hideNotification = (id = null) => ({type: HIDE_NOTIFICATION, id});

export const setFlashes = flashes => Object.keys(flashes).map(level => {
    return [].concat(flashes[level] || []).map(message => showNotification(message, level));
});
