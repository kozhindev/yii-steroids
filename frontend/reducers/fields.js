import _get from 'lodash-es/get';

import {FIELDS_BEFORE_FETCH, FIELDS_AFTER_FETCH} from '../actions/fields';

export default (state = {}, action) => {
    switch (action.type) {
        case FIELDS_BEFORE_FETCH:
            return {
                ...state,
                [action.fieldId]: {
                    props: null,
                    ...state[action.fieldId],
                    model: action.model,
                    attribute: action.attribute,
                    isLoading: true,
                },
            };

        case FIELDS_AFTER_FETCH:
            action.fields.forEach(field => {
                state[field.fieldId] = {
                    ...state[field.fieldId],
                    isLoading: false,
                    props: {
                        ..._get(state, `${field.fieldId}.props`),
                        ...field.props,
                    },
                };
            });
            return {...state};
    }

    return state;
};

export const getFieldProps = (state, fieldId) => _get(state, ['fields', fieldId, 'props']);
export const isFieldLoading = (state, fieldId) => !!_get(state, ['fields', fieldId, 'isLoading']);
