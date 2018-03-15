import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import './ButtonView.scss'

const bem = html.bem('ButtonView');

export default class ButtonView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        type: PropTypes.oneOf(['button', 'submit']),
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        color: PropTypes.oneOf(['default', 'primary', 'info', 'success', 'warning', 'danger']),
        url: PropTypes.string,
        onClick: PropTypes.func,
        disabled: PropTypes.bool,
        submitting: PropTypes.bool,
        block: PropTypes.bool,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    render() {
        console.log(this.props.block);
        return (
            <button className={bem(
                bem.block({
                    color: this.props.color,
                    size: this.props.size,
                    disabled: this.props.disabled,
                    submitting: this.props.submitting,
                }),
                this.props.className,
                'btn',
                'btn-' + this.props.size,
                'btn-' + this.props.color,
                 this.props.block ? 'btn-block' : '',
            )}>
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
}
