import _get from 'lodash-es/get';
import _isMatch from 'lodash-es/isMatch';
import _every from 'lodash-es/every';
import _extend from 'lodash-es/extend';

import {
    LIST_INIT,
    LIST_BEFORE_FETCH,
    LIST_AFTER_FETCH,
    LIST_ITEM_UPDATE,
    LIST_DESTROY,
    LIST_TOGGLE_ITEM,
    LIST_TOGGLE_ALL,
} from '../actions/list';

export default (state = {}, action) => {
    switch (action.type) {
        case LIST_INIT:
            return {
                ...state,
                [action.listId]: {
                    meta: {},
                    selectedIds: {},
                    total: action.total || (action.items ? action.items.length : 0),
                    isFetched: !!action.items,
                    isLoading: false,
                    ...action,
                }
            };

        case LIST_BEFORE_FETCH:
            return {
                ...state,
                [action.listId]: {
                    ...state[action.listId],
                    ...action,
                    isLoading: true,
                }
            };

        case LIST_AFTER_FETCH:
            let items;
            const list = state[action.listId];

            if (list && list.items && list.loadMore && list.page > 1) {
                items = [].concat(list.items);
                action.items.forEach((entry, i) => {
                    const index = ((list.page - 1) * list.pageSize) + i;
                    items[index] = entry;
                });
            } else {
                items = [].concat(action.items);
            }

            return {
                ...state,
                [action.listId]: {
                    ...list,
                    ...action,
                    items,
                    isFetched: true,
                    isLoading: false,
                }
            };

        case LIST_ITEM_UPDATE:
            return {
                ...state,
                [action.listId]: {
                    ...state[action.listId],
                    items: state[action.listId].items.map(item => {
                        if (_isMatch(item, action.condition)) {
                            item = _extend({}, item, action.item);
                        }
                        return item;
                    }),
                }
            };

        case LIST_DESTROY:
            delete state[action.listId];
            return {
                ...state,
            };

        case LIST_TOGGLE_ITEM:
            return {
                ...state,
                [action.listId]: {
                    ..._get(state, [action.listId]),
                    selectedIds: {
                        ..._get(state, [action.listId, 'selectedIds']),
                        [action.itemId]: !_get(state, [action.listId, 'selectedIds', action.itemId]),
                    },
                },
            };

        case LIST_TOGGLE_ALL:
            const list4 = state[action.listId];
            if (list4) {
                const ids = list4.items.map(item => item[list4.primaryKey]) || [];
                const isAll = _every(ids.map(id => list4.selectedIds[id]));
                return {
                    ...state,
                    [action.listId]: {
                        ...list4,
                        selectedIds: ids.reduce((obj, id) => {
                            obj[id] = !isAll;
                            return obj;
                        }, {}),
                    },
                };
            }
            break;
    }

    return state;
};

export const getList = (state, listId) => state.list[listId] || null;
export const getIds = (state, listId) => {
    const list = state.list[listId];
    return list && list.items.map(item => item[list.primaryKey]) || [];
};
export const getCheckedIds = (state, listId) => {
    const list = state.list[listId];
    const selectedIds = list && list.selectedIds || {};
    return Object.keys(selectedIds).filter(id => selectedIds[id]);
};
export const isCheckedAll = (state, listId) => {
    const list = state.list[listId];
    return _every(getIds(state, listId).map(id => list.selectedIds[id]));
};
