import {
    TYPE_FIELDS_BEFORE_FETCH, 
    TYPE_FIELDS_AFTER_FETCH,
    TYPE_FIELDS_SET_META, 
    TYPE_FIELDS_ADD_SECURITY,
    TYPE_FIELDS_REMOVE_SECURITY
} from './actionTypes.d';
import MetaModel from './../models/Meta';
import FieldModel from './../models/Field';

//TODO: replace "any"
export interface IntBeforeFetch {
    type: TYPE_FIELDS_BEFORE_FETCH;
    fieldId: string;
    model: any;
    attribute: string;
}

export interface IntAfterFetch {
    type: TYPE_FIELDS_AFTER_FETCH;
    fields: Array<FieldModel>;
}

export interface IntSetMeta {
    type: TYPE_FIELDS_SET_META;
    meta: MetaModel;
}

export interface IntAddSecurity {
    type: TYPE_FIELDS_ADD_SECURITY;
    formId: string;
    params: object;
}

export interface IntRemoveSecurity {
    type: TYPE_FIELDS_REMOVE_SECURITY;
    formId: string;
}

