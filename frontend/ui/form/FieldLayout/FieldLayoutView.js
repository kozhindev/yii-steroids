import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('FieldLayoutView');

export default class FieldLayoutView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        required: PropTypes.bool,
        layout: PropTypes.oneOf(['default', 'inline', 'horizontal']),
        layoutCols: PropTypes.arrayOf(PropTypes.number),
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem.block()}>
                {this.props.label ? this.props.label + ':' : ''}
                {this.props.children}
            </div>
        );
    }

}
