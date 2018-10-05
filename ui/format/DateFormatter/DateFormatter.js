import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';

import {locale} from 'components';

export default class DateFormatter extends React.Component {

    static propTypes = {
        attribute: PropTypes.string,
        item: PropTypes.object,
        value: PropTypes.string,
        format: PropTypes.string,
    };

    static defaultProps = {
        format: 'LL',
    };

    render() {
        const value = this.props.value || _get(this.props.item, this.props.attribute);
        if (!value) {
            return null;
        }
        return locale.moment(value).format(this.props.format);
    }

}
