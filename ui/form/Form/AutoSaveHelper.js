import {initialize} from 'redux-form';
import _isString from 'lodash-es/isString';

import {clientStorage, store} from 'components';

export default class AutoSaveHelper {

    static STORAGE_KEY_PREFIX = 'Form';

    static restore(formId, initialValues) {
        const values = clientStorage.get(`${AutoSaveHelper.STORAGE_KEY_PREFIX}_${formId}`) || '';
        if (_isString(values) && values.substr(0, 1) === '{') {
            store.dispatch(initialize(formId, {
                ...JSON.parse(values),
                ...initialValues,
            }));
        }
    }

    static save(formId, values) {
        clientStorage.set(`${AutoSaveHelper.STORAGE_KEY_PREFIX}_${formId}`, JSON.stringify(values));
    }

    static remove(formId) {
        clientStorage.remove(`${AutoSaveHelper.STORAGE_KEY_PREFIX}_${formId}`);
    }

}
