import _filter from 'lodash-es/filter';
import _every from 'lodash-es/every';
import _extend from 'lodash-es/extend';

import {LIST_BEFORE_FETCH, LIST_AFTER_FETCH, LIST_ITEM_UPDATE, LIST_REMOVE, LIST_TOGGLE_ITEM, LIST_TOGGLE_ALL} from '../actions/list';

export default (state = {}, action) => {
    switch (action.type) {
        case LIST_BEFORE_FETCH:
            return {
                ...state,
                [action.id]: {
                    meta: {},
                    checkedIds: {},
                    total: action.items ? action.items.length : 0,
                    hasPagination: false,
                    ...(state[action.id] || {}),
                    ...action,
                    items: action.items
                        ? [].concat(action.items)
                        : state[action.id] && state[action.id].items || null,
                    isFetched: !!action.items || !!state[action.id],
                    isLoading: !action.items,
                }
            };

        case LIST_AFTER_FETCH:
            let items = [];
            const list = state[action.id];

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
                [action.id]: {
                    ...list,
                    ...action,
                    items,
                    isFetched: true,
                    isLoading: false,
                }
            };

        case LIST_ITEM_UPDATE:
            const list2 = state[action.id];
            const items2 = list2 && list2.items || [];

            _filter(items2, action.where).forEach((item, index) => {
                // Update old object, fix saved it in comet collection (if used)
                _extend(item, action.item);

                items2[items2.indexOf(item)] = {
                    ...item,
                    ...action.item,
                };
            });

            return {
                ...state,
                [action.id]: {
                    ...list2,
                    items: [].concat(items2),
                }
            };

        case LIST_REMOVE:
            delete state[action.id];
            return {
                ...state
            };

        case LIST_TOGGLE_ITEM:
            const list3 = state[action.id];
            if (list3) {
                const checkedIds = list3.checkedIds || {};
                return {
                    ...state,
                    [action.id]: {
                        ...list3,
                        checkedIds: {
                            ...checkedIds,
                            [action.itemId]: !checkedIds[action.itemId],
                        },
                    },
                };
            }
            break;

        case LIST_TOGGLE_ALL:
            const list4 = state[action.id];
            if (list4) {
                const ids = list4.items.map(item => item[list4.primaryKey]) || [];
                const isAll = _every(ids.map(id => list4.checkedIds[id]));
                return {
                    ...state,
                    [action.id]: {
                        ...list4,
                        checkedIds: ids.reduce((obj, id) => {
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

export const getList = (state, id) => state.list[id] || null;
export const getEntry = (state, listId, itemId) => {
    const list = state.list[listId];
    return list && list.items.find(item => item[list.primaryKey] === itemId) || null;
};
export const getIds = (state, listId) => {
    const list = state.list[listId];
    return list && list.items.map(item => item[list.primaryKey]) || [];
};
export const getCheckedIds = (state, listId) => {
    const list = state.list[listId];
    const checkedIds = list && list.checkedIds || {};
    return Object.keys(checkedIds).filter(id => checkedIds[id]);
};
export const isCheckedAll = (state, listId) => {
    const list = state.list[listId];
    return _every(getIds(state, listId).map(id => list.checkedIds[id]));
};