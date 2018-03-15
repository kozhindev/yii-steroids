import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';
import fieldHoc from '../fieldHoc';

@fieldHoc()
export default class InputField extends React.Component {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        attribute: PropTypes.string,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        type: PropTypes.oneOf(['text', 'email', 'hidden', 'phone', 'password']),
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    constructor() {
        super(...arguments);

        this._onChange = this._onChange.bind(this);
    }

    render() {
        // No render for hidden input
        if (this.props.type === 'hidden') {
            return null;
        }

        const InputFieldView = this.props.view || view.get('form.InputFieldView');
        return (
            <InputFieldView
                {...this.props}
                inputProps={{
                    name: this.props.input.name,
                    value: this.props.input.value,
                    onChange: this._onChange,
                    type: this.props.type,
                    placeholder: this.props.placeholder,
                    disabled: this.props.disabled,
                    ...this.props.inputProps,
                }}
            />
        );
    }

    _onChange(e) {
        const value = e.target.value;

        this.props.input.onChange(value);
        if (this.props.onChange) {
            this.props.onChange(value);
        }
    }

}
