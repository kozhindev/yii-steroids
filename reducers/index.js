import {combineReducers} from 'redux';
import {reducer as form} from 'redux-form';
import {routerReducer as routing} from 'react-router-redux';

import fields from './fields';
import list from './list';
import config from './config';
import notifications from './notifications';
import modal from './modal';

export {
    form,
    routing,
    fields,
    list,
    config,
    notifications,
    modal,
};

export default combineReducers(module.exports);