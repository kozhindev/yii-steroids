import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('InputFieldView');

export default class InputFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
            PropTypes.element,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        type: PropTypes.string,
        placeholder: PropTypes.string,
        isInvalid: PropTypes.bool,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <input
                className={bem(
                    bem.block({
                        size: this.props.size,
                    }),
                    'form-control',
                    'form-control-' + this.props.size,
                    this.props.isInvalid && 'is-invalid',
                    this.props.className
                )}
                {...this.props.inputProps}
                type={this.props.type}
                placeholder={this.props.placeholder}
                disabled={this.props.disabled}
                required={this.props.required}
            />
        );
    }

}
