import _get from 'lodash-es/get';

import {
    FIELDS_BEFORE_FETCH,
    FIELDS_AFTER_FETCH,
    FIELDS_SET_META,
    FIELDS_ADD_SECURITY,
    FIELDS_REMOVE_SECURITY
} from '../actions/actionTypes';
import {
    IntBeforeFetch,
    IntAfterFetch,
    IntSetMeta,
    IntAddSecurity,
    IntRemoveSecurity
} from '../actions/fields.d';
import {fieldsState} from '../state/initialState';
import RootStateModel from '../models/RootState';
import MetaModel from '../models/Meta';

type TypeFieldsAction = IntBeforeFetch | IntAfterFetch | IntSetMeta | IntAddSecurity | IntRemoveSecurity;

export default (state = fieldsState, action: TypeFieldsAction) => {
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
                    ...state.security,
                    [action.formId]: action.params,
                },
            };

        case FIELDS_REMOVE_SECURITY:
            return {
                ...state,
                security: {
                    ...state.security,
                    [action.formId]: null,
                },
            };

        case FIELDS_SET_META:
            return {
                ...state,
                meta: {
                    ...state.meta,
                    ...action.meta,
                },
            };
    }

    return state;
};

export const getFieldProps = (state: RootStateModel, fieldId: string) => _get(state, ['fields', 'props', fieldId, 'props']);
export const isFieldLoading = (state: RootStateModel, fieldId: string): boolean => !!_get(state, ['fields', 'props', fieldId, 'isLoading']);
export const isMetaFetched = (state: RootStateModel): boolean => _get(state, ['fields', 'meta']) !== null;
export const getMeta = (state: RootStateModel, name: string): MetaModel => _get(state, ['fields', 'meta', name]) || null;
export const getEnumLabels = (state: RootStateModel, name: string) => _get(state, ['fields', 'meta', name, 'labels']) || null;
export const getSecurity = (state: RootStateModel, formId: string) => _get(state, ['fields', 'security', formId]);
