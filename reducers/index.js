import {combineReducers} from 'redux';
import {reducer as form} from 'redux-form';

import auth from './auth';
import fields from './fields';
import list from './list';
import config from './config';
import notifications from './notifications';
import modal from './modal';
import routing from './routing';
import navigation from './navigation';
import screen from './screen';

export {
    form,
    auth,
    fields,
    list,
    config,
    notifications,
    modal,
    routing,
    navigation,
    screen,
};

export default asyncReducers => combineReducers({
    form,
    auth,
    fields,
    list,
    config,
    notifications,
    modal,
    routing,
    navigation,
    screen,
    ...asyncReducers,
});
