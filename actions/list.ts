//TODO: replace "any" and "object"

import _get from 'lodash-es/get';
import { Dispatch } from 'redux';

import {http} from './../components';
import {
    LIST_INIT,
    LIST_BEFORE_FETCH,
    LIST_AFTER_FETCH,
    LIST_ITEM_UPDATE,
    LIST_DESTROY,
    LIST_TOGGLE_ITEM,
    LIST_TOGGLE_ALL
} from './actionTypes';
import ListModel from './../models/List';
import ListItemModel from './../models/ListItem';

const lazyTimers: any = {};

const defaultFetchHandler = (list: ListModel) => {
    return http.send(list.actionMethod, list.action || location.pathname, {
        ...list.query,
        page: list.page,
        pageSize: list.pageSize,
        sort: list.sort,
    }).then((response: any) => response.data);
};

export const init = (listId: string, props: any) => (dispatch: Dispatch) => dispatch({
    action: props.action || props.action === '' ? props.action : null,
    actionMethod: props.actionMethod || 'post',
    onFetch: props.onFetch,
    page: props.defaultPage,
    pageSize: props.defaultPageSize,
    sort: props.defaultSort || null,
    total: props.total || null,
    query: props.query || null,
    items: props.items || null,
    loadMore: props.loadMore,
    primaryKey: props.primaryKey,
    listId,
    type: LIST_INIT,
});

export const fetch = (listId: string, params: object = {}) => (dispatch: any, getState: any) => {
    const list = {
        ..._get(getState(), ['list', listId]),
        ...params,
    };
    if (!list.action && list.action !== '') {
        return;
    }

    const onFetch = list.onFetch || defaultFetchHandler;

    return dispatch([
        {
            ...params,
            listId,
            type: LIST_BEFORE_FETCH,
        },
        onFetch(list).then((data: ListModel) => {
            if (!getState().list[listId]) {
                return [];
            }

            return {
                ...data,
                listId,
                type: LIST_AFTER_FETCH,
            };
        }),
    ]);
};

export const lazyFetch = (listId: string, params: object) => (dispatch: any) => {
    if (lazyTimers[listId]) {
        clearTimeout(lazyTimers[listId]);
    }
    lazyTimers[listId] = setTimeout(() => dispatch(fetch(listId, params)), 200);
};

export const setPage = (listId: string, page: number, loadMore: boolean) => fetch(listId, {
    page,
    loadMore,
});

export const setPageSize = (listId: string, pageSize: number) => fetch(listId, {
    page: 1,
    pageSize,
});

export const setSort = (listId: string, sort: object) => fetch(listId, {
    sort,
});

export const refresh = (listId: string) => fetch(listId);

export const update = (listId: string, item: ListItemModel<any>, condition: any) => ({
    item,
    condition,
    listId,
    type: LIST_ITEM_UPDATE,
});

export const destroy = (listId: string) => {
    if (lazyTimers[listId]) {
        clearTimeout(lazyTimers[listId]);
    }

    return {
        listId,
        type: LIST_DESTROY,
    };
};

export const toggleItem = (listId: string, itemId: string) => ({
    listId,
    itemId,
    type: LIST_TOGGLE_ITEM,
});

export const toggleAll = (listId: string) => ({
    listId,
    type: LIST_TOGGLE_ALL,
});
