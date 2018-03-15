import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('InputFieldView');

export default class InputFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
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
