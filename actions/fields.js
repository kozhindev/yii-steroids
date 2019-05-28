import _get from 'lodash-es/get';
import _isArray from 'lodash-es/isArray';

import {http} from 'components';

export const FIELDS_BEFORE_FETCH = 'FIELDS_BEFORE_FETCH';
export const FIELDS_AFTER_FETCH = 'FIELDS_AFTER_FETCH';
export const FIELDS_SET_META = 'FIELDS_SET_META';
export const FIELDS_ADD_SECURITY = 'FIELDS_ADD_SECURITY';
export const FIELDS_REMOVE_SECURITY = 'FIELDS_REMOVE_SECURITY';

let timer = null;
let queue = [];

export const normalizeName = name => name.replace(/\\/g, '.').replace(/^\./, '');

export const fetch = (fieldId, model, attribute, params = {}) => dispatch => {
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
    }, 10);
};

export const fetchMeta = (names, force = false) => (dispatch, getState) => {
    if (_isArray(names)) {
        throw new Error('This format is deprecated, use {models: ..., enums: ...} format.');
    }

    // Normalize names
    Object.keys(names).forEach(key => {
        names[key] = names[key].map(normalizeName);
    });

    const isMetaFetched = getState().fields.meta !== null;
    if (isMetaFetched && !force) {
        return;
    }

    // Send request
    return http.post('/api/steroids/meta-fetch', names)
        .then(meta => dispatch({
            type: FIELDS_SET_META,
            meta,
        }));
};

export const addSecurity = (formId, params) => ({
    type: FIELDS_ADD_SECURITY,
    formId,
    params,
});

export const removeSecurity = (formId) => ({
    type: FIELDS_REMOVE_SECURITY,
    formId,
});
