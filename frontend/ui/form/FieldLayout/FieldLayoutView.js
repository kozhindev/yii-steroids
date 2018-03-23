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
            <div className={bem(
                bem.block({
                    layout: this.props.layout
                }),
                'form-group',
                this.props.layout === 'horizontal' && 'row',
                this.props.layout === 'inline' && 'form-inline'
            )}>
                {this.props.label && (
                    <label className={bem(
                        bem.element('label', {
                            required: this.props.required
                        }),
                        this.props.layout === 'horizontal' && 'col-form-label text-right',
                        this.props.layout === 'horizontal' && 'col-' + this.props.layoutCols[0],
                        this.props.layout === 'inline' && 'sr-only',
                    )}>
                        {this.props.label + ':'}
                    </label>
                )}
                <div className={bem(
                    bem.element('field'),
                    this.props.layout === 'horizontal' && 'col-' + this.props.layoutCols[1],
                    this.props.layout === 'horizontal' && !this.props.label && 'offset-' + this.props.layoutCols[0])
                }>
                    {this.props.children}
                </div>
            </div>
        );
    }
}
