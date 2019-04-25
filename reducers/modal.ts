import _get from 'lodash-es/get';
import _values from 'lodash-es/values';

import { OPEN_MODAL, CLOSE_MODAL } from '../actions/actionTypes';
import { IntOpenModal, IntCloseModal} from '../actions/modal.d';
import {modalState} from '../state/initialState';
import RootStateModel from '../models/RootState';

type TypeModalAction = IntOpenModal | IntCloseModal;

export default (state = modalState, action: TypeModalAction) => {
    switch (action.type) {
        case OPEN_MODAL:
            return {
                opened: {
                    ...state.opened,
                    [action.id]: {
                        id: action.id,
                        modal: action.modal,
                        props: {
                            ..._get(state, `opened.${action.id}.props`),
                            ...action.props,
                        },
                    }
                }
            };

        case CLOSE_MODAL:
            if (action.id) {
                const opened = state.opened;
                delete opened[action.id];
                return {
                    opened,
                };
            } else {
                return {
                    opened: {},
                };
            }

        default:
            return state;
    }
};

export const getOpened = (state: RootStateModel) => _values(state.modal.opened);