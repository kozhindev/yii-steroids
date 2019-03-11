import {combineReducers} from 'redux';
import {reducer as form} from 'redux-form';

import fields from './fields';
import list from './list';
import config from './config';
import notifications from './notifications';
import modal from './modal';
import routing from './routing';
import navigation from './navigation';

export {
    form,
    fields,
    list,
    config,
    notifications,
    modal,
    routing,
    navigation,
};

export default asyncReducers => combineReducers({
    ...module.exports,
    ...asyncReducers,
});