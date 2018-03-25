import React from 'react';
import PropTypes from 'prop-types';
import DayPickerInput from 'react-day-picker/DayPickerInput';

import {html} from 'components';
const bem = html.bem('DateFieldView');

export default class DateFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        dateField: PropTypes.node,
        timeField: PropTypes.node,
        className: PropTypes.string,
    };

    render() {
        return (
            <div>
                {this.props.dateField}
                {this.props.timeField}
            </div>
        );
    }

}
