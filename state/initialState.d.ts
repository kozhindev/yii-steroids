import RoutesTreeItemModel from '../models/RoutesTreeItem';
import RouteModel from '../models/Route';
import ModalModel from '../models/Modal';
import MetaModel from '../models/Meta';
import NotificationModel from '../models/Notification';
import LocationModel from '../models/Location';

//TODO: replace "any" and "object"
export interface IntFieldsState {
    props: {
        [propsKey: string]: {
            [propsInnerKey: string]: any,
        }
    };
    security: object;
    meta: MetaModel | null;
    [key: string]: any; //TODO: find better solution
}

export interface IntListState {
    [listKey: string]: any
}

export interface IntConfigState {
}

export interface IntNotificationsState {
    items: Array<NotificationModel>;
}

export interface IntModalState {
    opened: {
        [key: string]: ModalModel,
    };
}

export interface IntRoutingState {
    location: null | LocationModel;
    action: null | string;
    routes: Array<RouteModel>;
}

export interface IntNavigationState {
    routesTree: null | RoutesTreeItemModel;
    params: {
        [key: string]: string | number | null | undefined | object,
    };
}