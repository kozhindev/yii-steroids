import React from 'react';
import PropTypes from 'prop-types';
import _isString from 'lodash-es/isString';

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
        link: PropTypes.bool,
        outline: PropTypes.bool,
        url: PropTypes.string,
        onClick: PropTypes.func,
        disabled: PropTypes.bool,
        submitting: PropTypes.bool,
        isLoading: PropTypes.bool,
        block: PropTypes.bool,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    render() {
        return this.props.link || this.props.url ? this.renderLink() : this.renderButton();
    }

    renderLink() {
        return (
            <a
                className={this._getClassName({link: true})}
                href={this.props.url}
                onClick={this.props.onClick}
            >
                {this.renderLabel()}
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
                {this.renderLabel()}
            </button>
        );
    }

    renderLabel() {
        return (
            <>
                {this.props.isLoading && (
                    <div className={bem.element('preloader')}>
                        <span
                            className='spinner-border spinner-border-sm'
                            role="status"
                            aria-hidden="true"
                        />
                    </div>
                )}
                <span
                    className={bem.element('label')}
                >
                    {this.props.icon && (
                        <span
                            className={bem(
                                bem.element('icon', !this.props.label && 'without-label'),
                                'material-icons'
                            )}
                            title={_isString(this.props.label) ? this.props.label : null}
                        >
                            {this.props.icon}
                        </span>
                    )}
                    {this.props.children}
                </span>
            </>
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
                'is-loading': this.props.isLoading,
                ...modifiers,
            }),
            this.props.className,
            !this.props.link && 'btn',
            this.props.size && 'btn-' + this.props.size,
            !this.props.link && 'btn-' + (this.props.outline ? 'outline-' : '') + this.props.color,
            this.props.block && 'btn-block',
            this.props.link && 'btn-link',
        );
    }
}
