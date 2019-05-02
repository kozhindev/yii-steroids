//TODO: replace "any" and "object"

import { Dispatch } from 'redux';
import { NOTIFICATIONS_SHOW, NOTIFICATIONS_CLOSING, NOTIFICATIONS_CLOSE } from './actionTypes';

type TStringOrNull = number | null;
type TNotificationLevel = 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'light' | 'dark';

let ID_COUNTER: number = 0;

export const showNotification = (message: string, level: TNotificationLevel = 'warning') => (dispatch: Dispatch) => {
    const id = ++ID_COUNTER;
    dispatch({type: NOTIFICATIONS_SHOW, id, message, level});
    setTimeout(() => dispatch({type: NOTIFICATIONS_CLOSE, id}), 10000);
};

export const setClosing = (id: TStringOrNull = null) => ({type: NOTIFICATIONS_CLOSING, id});

export const closeNotification = (id: TStringOrNull = null) => ({type: NOTIFICATIONS_CLOSE, id});

export const setFlashes = (flashes: any) => Object.keys(flashes).map(level => {
    return [].concat(flashes[level] || []).map(message => showNotification(message, level));
});
