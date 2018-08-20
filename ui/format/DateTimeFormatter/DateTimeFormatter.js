import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import _get from 'lodash-es/get';

import {locale} from 'components';

export default class DateTimeFormatter extends React.Component {

    static propTypes = {
        attribute: PropTypes.string,
        item: PropTypes.object,
        value: PropTypes.string,
        format: PropTypes.string,
        timeZone: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
    };

    static defaultProps = {
        format: 'LLL',
    };

    render() {
        const value = this.props.value || _get(this.props.item, this.props.attribute);
        if (!value) {
            return null;
        }

        const date = this.props.timeZone === false
            ? moment(value).locale(locale.language)
            : locale.moment(value);
        return date.format(this.props.format);
    }

}
