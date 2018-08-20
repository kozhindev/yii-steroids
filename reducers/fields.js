import _get from 'lodash-es/get';

import {FIELDS_BEFORE_FETCH, FIELDS_AFTER_FETCH, FIELDS_ADD_SECURITY} from '../actions/fields';

const initialState = {
    props: {},
    security: {},
};

export default (state = initialState, action) => {
    switch (action.type) {
        case FIELDS_BEFORE_FETCH:
            return {
                ...state,
                props: {
                    ...state.props,
                    [action.fieldId]: {
                        props: null,
                        ...state[action.fieldId],
                        model: action.model,
                        attribute: action.attribute,
                        isLoading: true,
                    },
                },
            };

        case FIELDS_AFTER_FETCH:
            action.fields.forEach(field => {
                state.props[field.fieldId] = {
                    ...state[field.fieldId],
                    isLoading: false,
                    props: {
                        ..._get(state, `${field.fieldId}.props`),
                        ...field.props,
                    },
                };
            });
            return {
                ...state,
                props: {
                    ...state.props,
                },
            };

        case FIELDS_ADD_SECURITY:
            return {
                ...state,
                security: {
                    ...state.props,
                    [action.formId]: action.fields,
                },
            };
    }

    return state;
};

export const getFieldProps = (state, fieldId) => _get(state, ['fields', 'props', fieldId, 'props']);
export const isFieldLoading = (state, fieldId) => !!_get(state, ['fields', 'props', fieldId, 'isLoading']);
export const getSecurityFields = (state, formId) => _get(state, ['fields', 'security', formId]);
