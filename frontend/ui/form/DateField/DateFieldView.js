import React from 'react';
import PropTypes from 'prop-types';
import DayPickerInput from 'react-day-picker/DayPickerInput';

import {html} from 'components';
const bem = html.bem('DateFieldView');
import './DateFieldView.scss';

export default class DateFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        pickerProps: PropTypes.object,
        className: PropTypes.string,
        isInvalid: PropTypes.bool,
    };

    render() {
        return (
            <div>
                <DayPickerInput
                    {...this.props.pickerProps}
                    inputProps={{
                        className: bem(
                            bem.block({
                                size: this.props.size,
                            }),
                            'form-control',
                            'form-control-' + this.props.size,
                            this.props.isInvalid && 'is-invalid',
                            this.props.className,
                        ),
                        disabled: this.props.disabled,
                        required: this.props.required,
                    }}
                />
            </div>
        );
    }

}
