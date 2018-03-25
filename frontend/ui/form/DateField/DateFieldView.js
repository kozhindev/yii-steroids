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
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        pickerProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <div>
                <DayPickerInput
                    {...this.props.pickerProps}
                    className='form-control'
                />
            </div>
        );
    }

}
