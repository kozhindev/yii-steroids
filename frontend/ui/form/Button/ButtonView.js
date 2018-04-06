import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import './ButtonView.scss';

const bem = html.bem('ButtonView');

export default class ButtonView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        type: PropTypes.oneOf(['button', 'submit']),
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        color: PropTypes.oneOf([
            'primary',
            'secondary',
            'success',
            'danger',
            'warning',
            'info',
            'light',
            'dark',
        ]),
        outline: PropTypes.bool,
        url: PropTypes.string,
        onClick: PropTypes.func,
        disabled: PropTypes.bool,
        submitting: PropTypes.bool,
        block: PropTypes.bool,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    render() {
        return this.props.url ? this.renderLink() : this.renderButton();
    }

    renderLink() {
        return (
            <a
                className={this._getClassName({link: true})}
                href={this.props.url}
            >
                {this.props.children}
            </a>
        );
    }

    renderButton() {
        return (
            <button
                type={this.props.type}
                disabled={this.props.disabled}
                onClick={this.props.onClick}
                className={this._getClassName()}
            >
                {this.props.icon && (
                    <span
                        className={bem(
                            bem.element('icon'),
                            this.props.icon,
                        )}
                    />
                )}
                {this.props.children}
            </button>
        );
    }
    
    _getClassName(modifiers) {
        return bem(
            bem.block({
                color: this.props.color,
                outline: this.props.outline,
                size: this.props.size,
                disabled: this.props.disabled,
                submitting: this.props.submitting,
                ...modifiers,
            }),
            this.props.className,
            'btn',
            'btn-' + this.props.size,
            'btn-' + (this.props.outline ? 'outline-' : '') + this.props.color,
            this.props.block ? 'btn-block' : '',
        );
    }
}
