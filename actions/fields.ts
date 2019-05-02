import _get from 'lodash-es/get';
import { Dispatch } from 'redux';

//TODO: replace "any" and "object"
import {http} from './../components';
import {
    FIELDS_BEFORE_FETCH,
    FIELDS_AFTER_FETCH,
    FIELDS_SET_META,
    FIELDS_ADD_SECURITY,
    FIELDS_REMOVE_SECURITY
} from './actionTypes';

let timer: number | null = null;
let queue: Array<{
    fieldId: string;
    model: any;
    attribute: string;
    params?: object;
}> = [];

export const fetch = (fieldId: string, model: any, attribute: string, params: object = {}) => (dispatch: Dispatch) => {
    model = _get(model, 'className', String(model));

    // Mark loading
    dispatch({
        type: FIELDS_BEFORE_FETCH,
        fieldId,
        model,
        attribute,
    });

    // Add to queue
    queue.push({fieldId, model, attribute, params});

    // Lazy send request
    if (timer) {
        clearTimeout(timer);
    }
    timer = setTimeout(() => {
        // Send request
        http.post('/api/steroids/fields-fetch', {fields: queue})
            .then(fields => dispatch({
                type: FIELDS_AFTER_FETCH,
                fields,
            }));

        // Clean queue
        queue = [];
    }, 10) as any;
};

export const fetchMeta = (names: Array<string>, force: boolean = false) => (dispatch: Dispatch, getState: any) => {
    const isMetaFetched: boolean = getState().fields.meta !== null;
    if (isMetaFetched && !force) {
        return;
    }

    // Send request
    return http.post('/api/steroids/meta-fetch', {names})
        .then(meta => dispatch({
            type: FIELDS_SET_META,
            meta,
        }));
};

export const addSecurity = (formId: string, params: object) => ({
    type: FIELDS_ADD_SECURITY,
    formId,
    params,
});

export const removeSecurity = (formId: string) => ({
    type: FIELDS_REMOVE_SECURITY,
    formId,
});
