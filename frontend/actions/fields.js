import _get from 'lodash-es/get';

import {http} from 'components';

export const FIELDS_BEFORE_FETCH = 'FIELDS_BEFORE_FETCH';
export const FIELDS_AFTER_FETCH = 'FIELDS_AFTER_FETCH';

let timer = null;
let queue = [];

export const fetch = (model, attribute, params = {}) => dispatch => {
    model = _get(model, 'className', String(model));

    // Mark loading
    dispatch({
        type: FIELDS_BEFORE_FETCH,
        model,
        attribute,
    });


    // Add to queue
    queue.push({model, attribute, params});

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
