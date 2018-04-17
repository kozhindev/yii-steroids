import React from 'react';
import PropTypes from 'prop-types';

import {locale} from 'components';
import viewHoc from '../viewHoc';

@viewHoc()
export default class DateFormatter extends React.Component {

    static propTypes = {
        value: PropTypes.string,
        format: PropTypes.string,
    };

    static defaultProps = {
        format: 'LL',
    };

    render() {
        if (!this.props.value) {
            return null;
        }
        return locale.moment(this.props.value).format(this.props.format);
    }

}
