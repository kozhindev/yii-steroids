import React from 'react';
import PropTypes from 'prop-types';
import Modal from 'react-modal';

import {html} from 'components';
import './ModalView.scss';

const bem = html.bem('ModalView');

export default class ModalView extends React.PureComponent {

    static propTypes = {
        onClose: PropTypes.func,
        children: PropTypes.node,
    };

    render() {
        return (
            <div className={bem.block()}>
                <Modal
                    isOpen={true}
                    className={bem.element('modal')}
                    overlayClassName={bem.element('overlay')}
                    ariaHideApp={false}
                    {...this.props}
                >
                    <div className={bem.element('inner')}>
                        <div className={bem.element('header')}>
                            <span className={bem.element('title')}>
                                {this.props.title}
                            </span>
                            <a
                                className={bem.element('close')}
                                href='javascript:void(0)'
                                onClick={this.props.onClose}
                            />
                        </div>
                        <div className={bem.element('content')}>
                            {this.props.children}
                        </div>
                    </div>
                </Modal>
            </div>
        );
    }

}