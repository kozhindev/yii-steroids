//TODO: replace "any" and "object"

import {
    TYPE_LIST_INIT,
    TYPE_LIST_BEFORE_FETCH,
    TYPE_LIST_AFTER_FETCH,
    TYPE_LIST_ITEM_UPDATE,
    TYPE_LIST_DESTROY,
    TYPE_LIST_TOGGLE_ITEM,
    TYPE_LIST_TOGGLE_ALL
} from './actionTypes.d';

export interface IntInit {
    type: TYPE_LIST_INIT;
    listId: string;
    actionMethod: string;
    primaryKey: string;
    action: string | null;
    sort: string | null;
    page: number;
    pageSize: number;
    total: number | null;
    query: object | null; // object?
    items: Array<any> | null; //any?
    onFetch: () => void;
    loadMore: boolean;
}

export interface IntBeforeFetch {
    type: TYPE_LIST_BEFORE_FETCH;
    listId: string;
    [paramsKey: string]: any;
}

export interface IntAfterFetch {
    type: TYPE_LIST_AFTER_FETCH;
    listId: string;
    [dataKey: string]: any;
}

export interface IntItemUpdate {
    type: TYPE_LIST_ITEM_UPDATE;
    item: any;
    condition: any;
    listId: string;
}

export interface IntDestroy {
    type: TYPE_LIST_DESTROY;
    listId: string;
}

export interface IntToggleItem {
    type: TYPE_LIST_TOGGLE_ITEM;
    listId: string;
    itemId: string;
}

export interface IntToggleAll {
    type: TYPE_LIST_TOGGLE_ALL;
    listId: string;
}

