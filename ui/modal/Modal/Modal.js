import React from 'react';
import PropTypes from 'prop-types';
import {ui} from 'components';


export default class Modal extends React.PureComponent {

    static propTypes = {
        onClose: PropTypes.func,
        view: PropTypes.func,
    };

    render() {
        const ModalView = this.props.view || ui.getView('modal.ModalView');

        return (
            <ModalView
                {...this.props}
                onClose={this.props.onClose}
            >
                {this.props.children}
            </ModalView>
        );
    }
}