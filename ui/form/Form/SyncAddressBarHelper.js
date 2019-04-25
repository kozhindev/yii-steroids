import queryString from 'query-string';
import _isArray from 'lodash/isArray';
import _isObject from 'lodash/isObject';
import _isBoolean from 'lodash/isBoolean';
import _isEqual from 'lodash/isEqual';
import {initialize} from 'redux-form';

import {store} from 'components';

export default class SyncAddressBarHelper {

    static restore(formId, initialValues) {
        const newValues = {
            ...initialValues,
            ...queryString.parse(location.hash),
        };
        if (!_isEqual(initialValues, newValues)) {
            store.dispatch(initialize(formId, newValues));
        }
    }

    /**
     * WARNING
     * Method incorrectly saves nested objects (e.g. {foo: [{bar: 1}]}
     * // @todo use 'qs' library instead of 'query-string'
     *
     * @param {*} values
     */
    static save(values) {
        values = {...values};

        Object.keys(values).map(key => {
            const value = values[key];

            if (_isObject(value) && !_isArray(value)) {
                delete values[key];
            } else if (_isBoolean(value)) {
                if (!value) {
                    delete values[key];
                } else {
                    values[key] = 1;
                }
            } else if (value === null) {
                delete values[key];
            }
        });

        location.hash = '#' + queryString.stringify(values);
    }

}
