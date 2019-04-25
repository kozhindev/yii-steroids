import {
    IntFieldsState,
    IntListState,
    IntConfigState,
    IntNotificationsState,
    IntModalState,
    IntRoutingState,
    IntNavigationState
} from './initialState.d';

export const fieldsState: IntFieldsState = {
    props: {},
    security: {},
    meta: null,
};

export const listState: IntListState = { };

export const configState: IntConfigState = { };

export const notificationsState: IntNotificationsState = {
    items: [],
};

export const modalState: IntModalState = {
    opened: {},
};

export const routingState: IntRoutingState = {
    location: null,
    action: null,
    routes: [],
};

export const navigationState: IntNavigationState = {
    routesTree: null,
    params: {},
};
