import { OPEN_MODAL, CLOSE_MODAL } from './actionTypes';

import ModalModel from '../models/Modal';
let idCounter: number = 0;

//TODO: replace "any" and "object"
export const openModal = (modal: ModalModel, props: any) => {
    let id: string | null = props ? props.modalId : null;
    if (!id) {
        modal.id = modal.id || 'modal-' + ++idCounter;
        id = modal.id;
    }

    return {
        type: OPEN_MODAL,
        id,
        modal,
        props,
    };
};
export const closeModal = (id: string) => ({type: CLOSE_MODAL, id});
