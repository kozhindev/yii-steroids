import {http, types} from 'components';

export const FORM_LIST_BEFORE_FETCH = 'FORM_LIST_BEFORE_FETCH';
export const FORM_LIST_AFTER_FETCH = 'FORM_LIST_AFTER_FETCH';
export const FORM_LIST_CLEAR_CACHE = 'FORM_LIST_CLEAR_CACHE';
export const FORM_LIST_BEFORE_AUTO_COMPLETE = 'FORM_LIST_BEFORE_AUTO_COMPLETE';
export const FORM_LIST_AFTER_AUTO_COMPLETE = 'FORM_LIST_AFTER_AUTO_COMPLETE';
export const FORM_LIST_SAVE_TO_CACHE = 'FORM_LIST_SAVE_TO_CACHE';
export const FORM_LIST_CACHE_ENTRIES = 'FORM_LIST_CACHE_ENTRIES';
export const FORM_LIST_COPY = 'FORM_LIST_COPY';

export const fetchByIds = (fieldId, ids, params = {}) => (dispatch, getState) => {
    const state = getState().formList;

    return dispatch([
        {
            fieldId,
            ids,
            type: FORM_LIST_BEFORE_FETCH,
        },
        fetchByIdsInternal(state, fieldId, ids, params)
            .then(entries => (
                entries
                    ? {entries, type: FORM_LIST_AFTER_FETCH}
                    : []
            ))
    ]);
};

export const clearCache = (fieldId, entryIds) => ({
    fieldId,
    entryIds,
    type: FORM_LIST_CLEAR_CACHE,
});

export const fetchAutoComplete = (fieldId, queryString, isAutoFetch, params = {}) => [
    {
        fieldId,
        type: FORM_LIST_BEFORE_AUTO_COMPLETE,
    },
    dispatch => {
        if (!isAutoFetch && !queryString) {
            return dispatch({
                entries: [],
                fieldId,
                type: FORM_LIST_AFTER_AUTO_COMPLETE,
            });
        }

        const {method, ...requestParams} = params;
        return dispatch(
            http.post(method || types.autoCompleteUrl, {
                ...requestParams,
                queryString,
                isAutoFetch,
            })
                .then(entries => ({
                    entries,
                    fieldId,
                    type: FORM_LIST_AFTER_AUTO_COMPLETE,
                }))
        );
    }
];

export const saveToCache = (fieldId, entries) => ({
    fieldId,
    entries,
    type: FORM_LIST_SAVE_TO_CACHE,
});

export const copy = (fromFieldId, toFieldId, entryIds) => ({
    fromFieldId,
    toFieldId,
    entryIds,
    type: FORM_LIST_COPY,
});

export const cacheEntries = (fieldId, entryIds) => ({
    fieldId,
    entryIds,
    type: FORM_LIST_CACHE_ENTRIES,
});

let timeout = null;
let lastCallback = null;
let requests = [];
const fetchByIdsInternal = (state, fieldId, ids, params) => {
    ids = [].concat(ids);

    const {getLabels} = require('../reducers/formList');
    const labels = getLabels(state, fieldId, ids);
    if (labels && labels.length === ids.length) {
        // No new data
        return Promise.resolve(null);
    }

    return new Promise(resolve => {
        if (timeout) {
            lastCallback(null);
            clearTimeout(timeout);
        }
        lastCallback = resolve;
        timeout = setTimeout(() => {
            // New data
            http.post(types.fetchUrl, {requests})
                .then(result => resolve(result));

            requests = [];
        }, 50);

        // Batch requests
        requests.push({
            fieldId,
            ids,
            ...params,
        });
    });
};

