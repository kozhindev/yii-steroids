import { NOTIFICATIONS_SHOW, NOTIFICATIONS_CLOSING, NOTIFICATIONS_CLOSE } from '../actions/actionTypes';
import { IntNotificationShow, IntNotificationClosing, IntNotificationClose} from '../actions/notifications.d';
import {notificationsState} from '../state/initialState';
import RootStateModel from '../models/RootState';
import NotificationModel from '../models/Notification';

type TypeNotificationAction = IntNotificationShow | IntNotificationClosing | IntNotificationClose;

export default (state = notificationsState, action: TypeNotificationAction) => {
    switch (action.type) {
        case NOTIFICATIONS_SHOW:
            return {
                ...state,
                items: []
                    .concat(state.items)
                    .filter(item => item.level !== action.level || item.message !== action.message) // unique
                    .concat([{
                        id: action.id,
                        level: action.level || 'info',
                        message: action.message,
                        isClosing: false,
                    }]),
            };

        case NOTIFICATIONS_CLOSING:
            return {
                ...state,
                items: [].concat(state.items).map(item => {
                    if (item.id === action.id) {
                        item.isClosing = true;
                    }
                    return item;
                }),
            };

        case NOTIFICATIONS_CLOSE:
            return {
                ...state,
                items: state.items.filter(item => item.id !== action.id),
            };

        default:
            return state;
    }
};

export const getNotifications = (state: RootStateModel) => state.notifications.items;
