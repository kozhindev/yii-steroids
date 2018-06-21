let ID_COUNTER = 0;

export const NOTIFICATIONS_SHOW = 'NOTIFICATIONS_SHOW';
export const NOTIFICATIONS_CLOSING = 'NOTIFICATIONS_CLOSING';
export const NOTIFICATIONS_CLOSE = 'NOTIFICATIONS_CLOSE';

export const showNotification = (message, level = 'warning') => dispatch => {
    const id = ++ID_COUNTER;
    dispatch({type: NOTIFICATIONS_SHOW, id, message, level});
    setTimeout(() => dispatch({type: NOTIFICATIONS_CLOSE, id}), 10000);
};

export const setClosing = (id = null) => ({type: NOTIFICATIONS_CLOSING, id});
export const closeNotification = (id = null) => ({type: NOTIFICATIONS_CLOSE, id});

export const setFlashes = flashes => Object.keys(flashes).map(level => {
    return [].concat(flashes[level] || []).map(message => showNotification(message, level));
});
