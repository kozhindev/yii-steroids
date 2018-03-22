import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {isSubmitting} from 'redux-form';

import {view} from 'components';

@connect(
    (state, props) => ({
        submitting: props.formId ? isSubmitting('myForm')(state) : !!props.submitting,
    })
)
class ButtonInternal extends React.PureComponent {

    render() {
        const ButtonView = this.props.view || view.get('form.ButtonView');
        const disabled = this.props.submitting || this.props.disabled;
        return (
            <ButtonView
                {...this.props}
                disabled={disabled}
                onClick={!disabled ? this.props.onClick : undefined}
            >
                {this.props.label || this.props.children}
            </ButtonView>
        );
    }

}

export default class Button extends React.PureComponent {

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

    static defaultProps = {
        type: 'button',
        size: 'md',
        color: 'primary',
        outline: false,
        disabled: false,
        submitting: false,
        block: false,
        className: '',
    };

    static contextTypes = {
        formId: PropTypes.string,
    };

    render() {
        return (
            <ButtonInternal
                {...this.props}
                formId={this.context.formId}
            />
        );
    }
}
