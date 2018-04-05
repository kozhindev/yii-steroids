import _get from 'lodash-es/get';

import {FIELDS_BEFORE_FETCH, FIELDS_AFTER_FETCH} from '../actions/fields';

const toKey = (model, attribute) => {
    model = _get(model, 'className', String(model));
    return model + '::' + attribute;
};

export default (state = {}, action) => {
    switch (action.type) {
        case FIELDS_BEFORE_FETCH:
            const key = toKey(action.model, action.attribute);
            return {
                ...state,
                [key]: {
                    props: null,
                    ...state[key],
                    model: action.model,
                    attribute: action.attribute,
                    isLoading: true,
                },
            };

        case FIELDS_AFTER_FETCH:
            action.fields.forEach(field => {
                const key = toKey(field.model, field.attribute);
                state[key] = {
                    ...state[key],
                    isLoading: false,
                    props: {
                        ..._get(state, `${key}.props`),
                        ...field.props,
                    },
                };
            });
            return {...state};
    }

    return state;
};

export const getFieldProps = (state, model, attribute) => _get(state, `fields.${toKey(model, attribute)}.props`);
export const isFieldLoading = (state, model, attribute) => !!_get(state, `fields.${toKey(model, attribute)}.isLoading`);
