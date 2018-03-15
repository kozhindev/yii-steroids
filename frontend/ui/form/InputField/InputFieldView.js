import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('InputFieldView');

export default class InputFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        type: PropTypes.oneOf(['text', 'email', 'hidden', 'phone', 'password']),
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <input
                className={bem(bem.block(), 'form-control', this.props.className)}
                {...this.props.inputProps}
            />
        );
    }

}
