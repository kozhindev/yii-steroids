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
class Button extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        type: PropTypes.oneOf(['button', 'submit']),
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        color: PropTypes.oneOf(['default', 'primary', 'info', 'success', 'warning', 'danger']),
        url: PropTypes.string,
        onClick: PropTypes.func,
        disabled: PropTypes.bool,
        submitting: PropTypes.bool,
    };

    static defaultProps = {
        type: 'button',
        size: 'md',
        color: 'default',
        disabled: false,
        submitting: false,
    };

    static contextTypes = {
        formId: PropTypes.string,
    };

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

export default class ButtonWrapper extends React.Component {

    static contextTypes = {
        formId: PropTypes.string.isRequired,
    };

    render() {
        return (
            <Button
                {...this.props}
                formId={this.context.formId}
            />
        );
    }
}
