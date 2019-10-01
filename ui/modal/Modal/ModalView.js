import React from 'react';
import PropTypes from 'prop-types';
import Modal from 'react-modal';

import {html} from 'components';

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
                    overlayClassName={bem.element('overlay')}
                    ariaHideApp={false}
                    {...this.props}
                    className={bem(
                        bem.element('modal'),
                        this.props.className
                    )}
                >
                    <div className={bem.element('inner')}>
                        <div className={bem.element('header')}>
                            <span className={bem.element('title')}>
                                {this.props.title}
                            </span>
                            <a
                                className={bem.element('close')}
                                href='#'
                                onClick={e => {
                                    e.preventDefault();
                                    this.props.onClose();
                                }}
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
