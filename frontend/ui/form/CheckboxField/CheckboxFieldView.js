import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('CheckboxFieldView');
import './CheckboxFieldView.scss';

export default class CheckboxFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), 'form-check')}>
                <label className={bem(
                    bem.element('label',{
                        required: this.props.required
                    }),
                    'form-check-label'
                )}>
                    <input
                        className={bem(
                            bem.element('input'),
                            'form-check-input',
                            this.props.className
                        )}
                        {...this.props.inputProps}
                        disabled={this.props.disabled}
                        required={this.props.required}
                    />
                    {this.props.label}
                </label>
            </div>
        );
    }

}
