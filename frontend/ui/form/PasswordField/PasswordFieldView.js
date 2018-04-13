import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import './PasswordFieldView.scss';

const bem = html.bem('PasswordFieldView');

export default class PasswordFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        security: PropTypes.bool,
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        isInvalid: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
        securityLevel: PropTypes.string,
        onShowPassword: PropTypes.func,
        onHidePassword: PropTypes.func,
    };

    render() {
        return (
            <div className={bem.block()}>
                <div className={bem.element('input-container')}>
                    <input
                        className={bem(
                            bem.element('input', {
                                size: this.props.size,
                            }),
                            'form-control',
                            'form-control-' + this.props.size,
                            this.props.isInvalid && 'is-invalid',
                            this.props.className
                        )}
                        {...this.props.inputProps}
                    />
                    {this.props.security && (
                        <span
                            className={bem(bem.element('icon-eye'), 'material-icons')}
                            onMouseDown={this.props.onShowPassword}
                            onMouseUp={this.props.onHidePassword}
                        >
                            remove_red_eye
                        </span>
                    )}
                </div>
                {this.props.security && (
                    <div className={bem.element('security-bar', this.props.securityLevel)}/>
                )}
            </div>
        );
    }

}
