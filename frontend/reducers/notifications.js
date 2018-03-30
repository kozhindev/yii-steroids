import {SHOW_NOTIFICATION, HIDE_NOTIFICATION} from 'actions/notifications';

export default (state = {}, action) => {
    switch (action.type) {
        case SHOW_NOTIFICATION:
            return {
                ...state,
                [action.id]: {
                    id: action.id,
                    level: action.level,
                    message: action.message,
                },
            };

        case HIDE_NOTIFICATION:
            if (!action.id) {
                return {};
            }

            delete state[action.id];
            return {...state};

        default:
            return state;
    }
};