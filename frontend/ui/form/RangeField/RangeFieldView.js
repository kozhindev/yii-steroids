import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('RangeFieldView');

export default class RangeFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        placeholderFrom: PropTypes.string,
        placeholderTo: PropTypes.string,
        disabled: PropTypes.bool,
        inputFromProps: PropTypes.object,
        inputToProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <input
                    className='form-control'
                    {...this.props.inputFromProps}
                />
                -
                <input
                    className='form-control'
                    {...this.props.inputToProps}
                />
            </div>
        );
    }

}
