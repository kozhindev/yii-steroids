import React from 'react';
import PropTypes from 'prop-types';

import {ui} from 'components';
import fieldHoc from '../fieldHoc';

export default
@fieldHoc({
    componentId: 'form.NumberField',
})
class NumberField extends React.PureComponent {

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
        min: PropTypes.number,
        max: PropTypes.number,
        step: PropTypes.number,
        placeholder: PropTypes.string,
        isInvalid: PropTypes.bool,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    static defaultProps = {
        disabled: false,
        required: false,
        className: '',
        placeholder: '',
        min: null,
        max: null,
        step: null,
        errors: [], //for storybook
    };

    render() {
        const NumberFieldView = this.props.view || ui.getView('form.NumberFieldView') || ui.getView('form.InputFieldView');
        return (
            <NumberFieldView
                {...this.props}
                inputProps={{
                    name: this.props.input.name,
                    value: this.props.input.value || '',
                    onChange: e => this.props.input.onChange(e.target.value),
                    type: 'number',
                    min: this.props.min,
                    max: this.props.max,
                    step: this.props.step,
                    placeholder: this.props.placeholder,
                    disabled: this.props.disabled,
                    ...this.props.inputProps,
                }}
            />
        );
    }

}
