import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _isFunction from 'lodash/isFunction';
import {Modal} from 'reactstrap';

import {ui} from 'components';
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
        className: PropTypes.string,
        view: PropTypes.func,
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

        // Find real component class (not wrapped by redux.connect())
        const BodyComponent = Body.WrappedComponent || Body;

        const modalProps = _isFunction(BodyComponent.getModalProps) ? BodyComponent.getModalProps(item.props) : {};

        const ModalView = this.props.view || ui.getView('modal.ModalView');
        return (
            <Modal
                {...modalProps}
                key={item.id}
                isOpen={true}
                toggle={() => this.closeModal(item)}
            >
                <ModalView
                    {...this.props}
                    {...item.props}
                    onClose={() => this.closeModal(item)}
                >
                    <Body
                        {...item.props}
                        onClose={() => this.closeModal(item)}
                    />
                </ModalView>
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