import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('NumberFieldView');

export default class NumberFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        min: PropTypes.number,
        max: PropTypes.number,
        step: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
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
            />
        );
    }

}
