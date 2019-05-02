import { TYPE_NOTIFICATIONS_SHOW, TYPE_NOTIFICATIONS_CLOSING, TYPE_NOTIFICATIONS_CLOSE } from './actionTypes.d';

export interface IntNotificationShow {
    type: TYPE_NOTIFICATIONS_SHOW;
    id: string;
    message: string;
    level: string;
}

export interface IntNotificationClosing {
    type: TYPE_NOTIFICATIONS_CLOSING;
    id: string;
}

export interface IntNotificationClose {
    type: TYPE_NOTIFICATIONS_CLOSE;
    id: string;
}