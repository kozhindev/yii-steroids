import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';

import Modal from '../Modal/Modal';
import {closeModal} from '../../../actions/modal';
import {getOpened} from '../../../reducers/modal';

export default
@connect(
    state => ({
        opened: getOpened(state),
    })
)
class ModalWrapper extends React.PureComponent {
    static propTypes = {
        opened: PropTypes.arrayOf(PropTypes.shape({
            modal: PropTypes.func,
            props: PropTypes.object,
        })),
    };

    render() {
        return (
            <span>
                {this.props.opened.map(item => this.renderModal(item))}
            </span>
        );
    }

    renderModal(item) {
        const Body = item.modal;

        return (
            <Modal
                key={item.id}
                onClose={() => this.closeModal(item)}
                {...item.props}
            >
                <Body
                    {...item.props}
                    onClose={() => this.closeModal(item)}
                />
            </Modal>
        );
    }

    closeModal(item) {
        if (item.props && item.props.onClose) {
            item.props.onClose();
        }
        this.props.dispatch(closeModal(item.id));
    }
}