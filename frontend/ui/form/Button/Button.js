import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';
export default class Button extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        type: PropTypes.oneOf(['button', 'submit']),
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        color: PropTypes.oneOf(['default', 'primary', 'info', 'success', 'warning', 'danger']),
        url: PropTypes.string,
        onClick: PropTypes.func,
        disabled: PropTypes.bool,
    };

    static defaultProps = {
        type: 'button',
        size: 'md',
        color: 'default',
        disabled: false,
    };

    render() {
        const ButtonView = this.props.view || view.get('form.ButtonView');
        return (
            <ButtonView>
                {this.props.label || this.props.children}
            </ButtonView>
        );
    }

}
