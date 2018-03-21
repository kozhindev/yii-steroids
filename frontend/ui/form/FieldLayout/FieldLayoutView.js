import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import './FieldLayoutView.scss'
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
                {this.props.label && (
                    <label className={bem.element('label', {required: this.props.required})}>
                        {this.props.label + ':'}
                    </label>
                )}
                {this.props.children}
            </div>
        );
    }

}
