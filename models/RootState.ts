import {FormReducer} from 'redux-form';

import {
    IntFieldsState,
    IntListState,
    IntConfigState,
    IntNotificationsState,
    IntModalState,
    IntRoutingState,
    IntNavigationState,
} from './../state/initialState.d';

export default interface RootStateModel {
    form: FormReducer;
    fields: IntFieldsState;
    list: IntListState;
    config: IntConfigState;
    notifications: IntNotificationsState;
    modal: IntModalState;
    routing: IntRoutingState;
    navigation: IntNavigationState;
}