import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('TextFieldView');

export default class TextFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <textarea
                className={bem(bem.block(), 'form-control', this.props.className)}
                {...this.props.inputProps}
            />
        );
    }

}
