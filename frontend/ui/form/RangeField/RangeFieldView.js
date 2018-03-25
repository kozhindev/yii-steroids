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
        fromField: PropTypes.node,
        toField: PropTypes.node,
        disabled: PropTypes.bool,
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
                {this.props.fromField}
                -
                {this.props.toField}
            </div>
        );
    }

}
