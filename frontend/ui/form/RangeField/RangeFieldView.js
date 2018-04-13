import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('RangeFieldView');

export default class RangeFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        fromField: PropTypes.node,
        toField: PropTypes.node,
        disabled: PropTypes.bool,
        className: PropTypes.string,
        isInvalid: PropTypes.bool,
    };

    render() {
        return (
            <div className={bem(
                bem.block({
                    size: this.props.size,
                }),
                this.props.className,
                'row align-items-center'
            )}>
                <div className='col'>
                    {this.props.fromField}
                </div>
                -
                <div className='col'>
                    {this.props.toField}
                </div>
            </div>
        );
    }

}
