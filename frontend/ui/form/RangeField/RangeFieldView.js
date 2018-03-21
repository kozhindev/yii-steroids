import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('RangeFieldView');

export default class RangeFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        placeholderFrom: PropTypes.string,
        placeholderTo: PropTypes.string,
        disabled: PropTypes.bool,
        inputFromProps: PropTypes.object,
        inputToProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(
                bem.block({
                    size: this.props.size,
                }),
                this.props.className
            )}>
                <input
                    className={'form-control form-control-' + this.props.size}
                    {...this.props.inputFromProps}
                />
                -
                <input
                    className={'form-control form-control-' + this.props.size}
                    {...this.props.inputToProps}
                />
            </div>
        );
    }

}
