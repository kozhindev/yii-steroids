export const SCREEN_SET_WIDTH = 'SCREEN_SET_WIDTH';
export const SCREEN_SET_MEDIA = 'SCREEN_SET_MEDIA';

export const setMedia = media => ({
    type: SCREEN_SET_MEDIA,
    media,
});

let timer = null;
export const setWidth = width => dispatch => {
    if (timer) {
        clearTimeout(timer);
    }

    timer = setTimeout(() => {
        dispatch({
            type: SCREEN_SET_WIDTH,
            width,
        });
    }, 100);
};
