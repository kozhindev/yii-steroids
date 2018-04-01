import _get from 'lodash-es/get';
import _pick from 'lodash-es/pick';
import _keyBy from 'lodash-es/keyBy';
import _find from 'lodash-es/find';
import _filter from 'lodash-es/filter';
import _values from 'lodash-es/values';

import {
    FORM_LIST_BEFORE_FETCH,
    FORM_LIST_AFTER_FETCH,
    FORM_LIST_CLEAR_CACHE,
    FORM_LIST_BEFORE_AUTO_COMPLETE,
    FORM_LIST_AFTER_AUTO_COMPLETE,
    FORM_LIST_SAVE_TO_CACHE,
    FORM_LIST_CACHE_ENTRIES,
    FORM_LIST_COPY,
} from '../actions/formList';

const initialState = {
    cache: {}, // {fieldId: {entries: {id: object, ..}}, ..}
    autoComplete: {}, // {fieldId: {isLoading, ..., entries: [{id, label, ..}, ..]}, ..}
};

export default (state = initialState, action) => {
    switch (action.type) {
        case FORM_LIST_BEFORE_FETCH:
            return {
                ...state,
                cache: {
                    ...state.cache,
                    [action.fieldId]: {
                        entries: state.cache[action.fieldId] && state.cache[action.fieldId].entries || {},
                    }
                }
            };

        case FORM_LIST_AFTER_FETCH:
            const cache = {...state.cache};
            Object.keys(action.entries).forEach(fieldId => {
                cache[fieldId] = {
                    ...cache[fieldId],
                    entries: {
                        ...(cache[fieldId] && cache[fieldId].entries),
                        ..._keyBy(action.entries[fieldId], 'id'),
                    },
                };
            });

            return {
                ...state,
                cache,
            };

        case FORM_LIST_CLEAR_CACHE:
            const entries2 = {
                ...state.cache[action.fieldId].entries,
            };
            action.entryIds.forEach(id => {
                delete entries2[id];
            });

            return {
                ...state,
                cache: {
                    ...state.cache,
                    [action.fieldId]: {
                        entries: entries2,
                    }
                }
            };

        case FORM_LIST_BEFORE_AUTO_COMPLETE:
            return {
                ...state,
                autoComplete: {
                    ...state.autoComplete,
                    [action.fieldId]: {
                        isFetched: !!state.autoComplete[action.fieldId],
                        isLoading: true,
                        entries: state.autoComplete[action.fieldId] && state.autoComplete[action.fieldId].entries || [],
                    }
                }
            };

        case FORM_LIST_AFTER_AUTO_COMPLETE:
            return {
                ...state,
                autoComplete: {
                    ...state.autoComplete,
                    [action.fieldId]: {
                        isFetched: !!state.autoComplete[action.fieldId],
                        isLoading: true,
                        entries: action.entries,
                    }
                }
            };

        case FORM_LIST_SAVE_TO_CACHE:
            return {
                ...state,
                cache: {
                    ...state.cache,
                    [action.fieldId]: {
                        entries: {
                            ...(state.cache[action.fieldId] && state.cache[action.fieldId].entries || {}),
                            ...action.entries,
                        },
                    }
                }
            };

        case FORM_LIST_CACHE_ENTRIES:
            const autoCompleteList = _get(state, `autoComplete.${action.fieldId}.entries`);
            const entries = _filter(autoCompleteList, entry => action.entryIds.indexOf(entry.id) !== -1);
            return {
                ...state,
                cache: {
                    ...state.cache,
                    [action.fieldId]: {
                        entries: {
                            ...(state.cache[action.fieldId] && state.cache[action.fieldId].entries || {}),
                            ..._keyBy(entries, 'id'),
                        },
                    }
                }
            };

        case FORM_LIST_COPY:
            const fromEntries = _get(state, `cache.${action.fromFieldId}.entries`);
            const fromAutoCompleteList = _get(state, `autoComplete.${action.fromFieldId}.entries`);
            const entries3 = {};
            [].concat(action.entryIds || []).forEach(id => {
                entries3[id] = fromEntries[id] || _find(fromAutoCompleteList, entry => entry.id === id);
            });
            return {
                ...state,
                cache: {
                    ...state.cache,
                    [action.toFieldId]: {
                        entries: {
                            ..._get(state, `cache.${action.toFieldId}.entries`),
                            ...entries3,
                        },
                    }
                }
            };
    }

    return state;
};

export const getAutoComplete = (state, fieldId) => _get(state, `formList.autoComplete.${fieldId}.entries`);
export const getEntries = (state, fieldId, entryIds) => _values(_pick(_get(state, `formList.cache.${fieldId}.entries`), entryIds));
export const getLabels = (state, fieldId, entryIds) => {
    const entries = getEntries(state, fieldId, entryIds);
    if (entries.length === 0) {
        return null;
    }
    return entries.reduce((obj, entry) => {
        obj[entry.id] = entry.label;
        return obj;
    }, {});
};
