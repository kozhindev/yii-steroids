import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import _isEqual from 'lodash-es/isEqual';

import {ui} from 'components';
import fieldHoc from '../fieldHoc';
import DateField from '../DateField';
import InputField from '../InputField';

export default
@fieldHoc({
    componentId: 'form.DateTimeField',
})
class DateTimeField extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        attribute: PropTypes.string,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        displayDateFormat: PropTypes.string,
        valueDateFormat: PropTypes.string,
        timeFormat: PropTypes.string,
        dateProps: PropTypes.object,
        timeProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
        isInvalid: PropTypes.bool,
    };

    static defaultProps = {
        disabled: false,
        required: false,
        className: '',
        displayDateFormat: 'DD.MM.YYYY',
        valueDateFormat: 'YYYY-MM-DD',
        timeFormat: 'HH:mm',
        errors: [], //for storybook
    };

    constructor() {
        super(...arguments);

        this.state = this._parseToState(this.props);
    }

    componentWillReceiveProps(nextProps) {
        const newState = this._parseToState(nextProps);
        if (!_isEqual(this.state, newState)) {
            this.setState(newState);
        }
    }

    render() {
        const DateFieldInternal = DateField.WrappedComponent;
        const InputFieldInternal = InputField.WrappedComponent;

        const DateTimeFieldView = this.props.view || ui.getView('form.DateTimeFieldView');
        return (
            <DateTimeFieldView
                {...this.props}
                dateField={(
                    <DateFieldInternal
                        isInvalid={this.props.isInvalid}
                        size={this.props.size}
                        required={this.props.required}
                        disabled={this.props.disabled}
                        displayFormat={this.props.displayDateFormat}
                        valueFormat={this.props.valueDateFormat}
                        input={{
                            name: '',
                            value: this.state.date,
                            onChange: value => this._onChange({date: value}),
                        }}
                        {...this.props.dateProps}
                    />
                )}
                timeField={(
                    <InputFieldInternal
                        isInvalid={this.props.isInvalid}
                        size={this.props.size}
                        required={this.props.required}
                        disabled={this.props.disabled}
                        input={{
                            name: '',
                            value: this.state.time,
                            onChange: value => this._onChange({time: value}),
                        }}
                        {...this.props.timeProps}
                    />
                )}
            />
        );
    }

    _onChange(data) {
        this.setState(data, () => {
            const momentDate = this._parseDate(this.state.date + ' ' + this.state.time);
            const format = this.props.valueDateFormat + ' ' + this.props.timeFormat;
            if (momentDate) {
                this.props.input.onChange(momentDate.format(format));
            }
        });
    }

    _parseToState(props) {
        const momentDate = this._parseDate(props.input.value);
        return {
            date: momentDate ? momentDate.format(props.valueDateFormat) : null,
            time: (momentDate || moment().startOf('day')).format(props.timeFormat),
        };
    }

    _parseDate(date) {
        const formats = [
            this.props.displayDateFormat + ' ' + this.props.timeFormat,
            this.props.valueDateFormat + ' ' + this.props.timeFormat,
        ];
        const format = formats.find(format => {
            return date && date.length === format.length && moment(date, format).isValid();
        });
        return format ? moment(date, format) : null;
    }

}
