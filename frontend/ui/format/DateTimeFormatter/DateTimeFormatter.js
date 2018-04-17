import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';

import {locale} from 'components';
import viewHoc from '../viewHoc';

@viewHoc()
export default class DateTimeFormatter extends React.Component {

    static propTypes = {
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
        if (!this.props.value) {
            return null;
        }
        const date = this.props.timeZone === false ? moment(this.props.value) : locale.moment(this.props.value);
        return date.format(this.props.format);
    }

}
