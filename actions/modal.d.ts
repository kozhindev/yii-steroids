import { TYPE_OPEN_MODAL, TYPE_CLOSE_MODAL } from './actionTypes.d';

//TODO: replace "any" and "object"
export interface IntOpenModal {
    type: TYPE_OPEN_MODAL;
    id: string;
    modal: string;
    props?: object;
}

export interface IntCloseModal {
    type: TYPE_CLOSE_MODAL;
    id: string;
}

