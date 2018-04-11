import React from 'react';
import PropTypes from 'prop-types';
import _isArray from 'lodash-es/isArray';
import _isFunction from 'lodash-es/isFunction';
import _isObject from 'lodash-es/isObject';

import viewHoc from '../viewHoc';

@viewHoc()
export default class EnumFormatter extends React.Component {

    static propTypes = {
        value: PropTypes.string,
        items: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.shape({
                id: PropTypes.oneOfType([
                    PropTypes.number,
                    PropTypes.string,
                ]),
                label: PropTypes.string,
            })),
            PropTypes.func, // Enum
        ]),
    };

    /**
     * @param {array|function} items
     * @param {string|number} id
     * @returns {*}
     */
    static getLabel(items, id) {
        // Array
        if (_isArray(items)) {
            const finedItem = items.find(item => item.id === id);
            return finedItem ? finedItem.label : null;
        }

        // Enum
        if (_isObject(items) && _isFunction(items.getLabel)) {
            return items.getLabel(id);
        }

        return null;
    }

    render() {
        return EnumFormatter.getLabel(this.props.items, this.props.value);
    }

}
