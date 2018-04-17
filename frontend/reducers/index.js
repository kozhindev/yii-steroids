import {combineReducers} from 'redux';
import {reducer as form} from 'redux-form';
import fields from './fields';
import list from './list';
import config from './config';
import notifications from './notifications';
import modal from './modal';

export {
    form,
    fields,
    list,
    config,
    notifications,
    modal,
};

export default combineReducers(module.exports);