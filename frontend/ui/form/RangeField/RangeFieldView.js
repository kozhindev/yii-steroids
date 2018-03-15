import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('RangeFieldView');

export default class RangeFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
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
