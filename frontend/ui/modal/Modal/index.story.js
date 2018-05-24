import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {withReadme} from 'storybook-readme';
import {text} from '@storybook/addon-knobs/react';

import Button from '../../form/Button/index';
import Modal from './Modal';
import ModalWrapper from '../ModalWrapper/index';
import {openModal} from '../../../actions/modal';

import README from './README.md';


class ModalComponent extends React.Component {
    render() {
        return (
            <Modal
                {...this.props}
                title={this.props.title}
            />
        );
    }
}

@connect()
class ButtonOpenModal extends React.Component {
    static PropTypes = {
        modalTitle: PropTypes.string,
    };

    render() {
        return (
            <Button
                label='Open modal'
                onClick={() => this.props.dispatch(openModal(ModalComponent, {
                    title: this.props.modalTitle
                }))}
            />
        );
    }
}

storiesOf('Modal', module)
    .addDecorator(withReadme(README))
    .add('Modal', context => (
        <div>
            <ModalWrapper/>
            {withInfo()(() => (
                <ButtonOpenModal
                    modalTitle={text('Modal Title', 'Modal Title')}
                />
            ))(context)}
        </div>
    ));
