import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('CheckboxFieldView');
import './CheckboxFieldView.scss';

export default class CheckboxFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        isInvalid: PropTypes.bool,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(
                bem.block(),
                'custom-control',
                'custom-checkbox'
            )}>
                <input
                    className={bem(
                        bem.element('input'),
                        'custom-control-input',
                        this.props.isInvalid && 'is-invalid',
                        this.props.className
                    )}
                    id={this.props.fieldId + '_' + 'checkbox'}
                    {...this.props.inputProps}
                    disabled={this.props.disabled}
                    required={this.props.required}
                />
                <label
                    className={bem(
                        bem.element('label'),
                        'custom-control-label'
                    )}
                    htmlFor={this.props.fieldId + '_' + 'checkbox'}
                >
                    <span className={bem.element('label-text', {required: this.props.required})}>
                        {this.props.label}
                    </span>
                </label>
            </div>
        );
    }
}
