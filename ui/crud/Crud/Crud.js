import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Router} from '../../nav';
import _get from 'lodash-es/get';
import _isFunction from 'lodash-es/isFunction';
import _isString from 'lodash-es/isString';
import {push} from 'react-router-redux';

import {http, ui} from 'components';
import {refresh} from '../../../actions/list';
import {getCurrentRoute} from '../../../reducers/routing';
import Grid from '../../list/Grid';

export default
@connect(
    state => ({
        route: getCurrentRoute(state),
    })
)
class Crud extends React.PureComponent {

    static propTypes = {
        crudId: PropTypes.string.isRequired,
        controls: PropTypes.array,
        listProps: PropTypes.object,
        formProps: PropTypes.object,
        className: PropTypes.string,
        formView: PropTypes.func,
        wrapperView: PropTypes.func,
        primaryKey: PropTypes.string,
        route: PropTypes.shape({
            id: PropTypes.string,
            isExact: PropTypes.bool,
            params: PropTypes.object,
            path: PropTypes.string,
            url: PropTypes.string,
        }),
    };

    static defaultProps = {
        primaryKey: PropTypes.stirng,
    };

    render() {
        const CrudWrapperView = this.props.wrapperView || ui.getView('crud.CrudWrapperView');
        const CrudFormView = this.props.formView || ui.getView('crud.CrudFormView');
        return (
            <Router
                wrapperView={CrudWrapperView}
                wrapperProps={{
                    ...this.props,
                    controls: this.getControls('list'),
                }}
                routes={[
                    {
                        id: 'index',
                        exact: true,
                        path: '/',
                        component: Grid,
                        componentProps: {
                            listId: this.props.crudId,
                            primaryKey: this.props.primaryKey,
                            emptyText: __('Нет записей'),
                            ...this.props.listProps,
                            actions: (item, primaryKey) => this.getControls('item', item[primaryKey]),
                        },
                    },
                    {
                        id: 'create',
                        path: '/create',
                        component: CrudFormView,
                        componentProps: {
                            formProps: {
                                formId: this.props.crudId,
                                ...this.props.formProps,
                                onComplete: () => this.props.dispatch(push('/')),
                            },
                        },
                    },
                    {
                        id: 'update',
                        path: '/:id/update',
                        component: CrudFormView,
                        componentProps: {
                            formProps: {
                                formId: this.props.crudId,
                                ...this.props.formProps,
                                onComplete: () => this.props.dispatch(push('/')),
                            },
                        },
                    },
                ]}
            />
        );
    }

    getControls(actionType, itemId) {
        itemId = itemId || _get(this.props, 'route.params.id') || '';

        const actionId = _get(this.props, 'route.id');
        const available = this.props.controls.map(item => item.id);
        const defaultItems = {
            index: {
                actionType: 'list',
                label: __('К списку'),
                icon: 'keyboard_arrow_left',
                to: '/',
                color: 'secondary',
                outline: true,
                visible: actionId !== 'index',
            },
            create: {
                actionType: 'list',
                label: __('Добавить'),
                icon: 'add_circle',
                to: '/create',
                color: 'success',
                outline: true,
                visible: actionId === 'index' && available.indexOf('create') !== -1 && actionType === 'list',
            },
            view: {
                label: __('Просмотр'),
                color: 'secondary',
                outline: true,
                to: `/${itemId}`,
                visible: available.indexOf('view') !== -1,
            },
            update: {
                label: __('Редактировать'),
                color: 'secondary',
                outline: true,
                to: `/${itemId}/update`,
                visible: available.indexOf('update') !== -1,
            },
            delete: {
                icon: 'delete',
                label: __('Удалить'),
                confirm: __('Удалить запись?'),
                color: 'danger',
                outline: true,
                visible: available.indexOf('delete') !== -1,
                position: 'right',
                onClick: () => this._onDelete(itemId),
            },
        };

        const controls = [].concat(this.props.controls);
        if (available.indexOf('index') === -1) {
            controls.unshift({id: 'index'});
        }

        return controls
            .map(item => {
                if (!item) {
                    return null;
                }

                // Merge with defaults
                item = {
                    actionType: 'item', // Default action type
                    url: null, // TODO Reset url for grid actions
                    ...defaultItems[item.id],
                    ...item,
                };

                // Normalize url
                ['to', 'url'].forEach(key => {
                    const patternKey = key + 'Pattern';
                    if (_isFunction(item[patternKey])) {
                        item[key] = item[patternKey].call(null, itemId, item);
                    } else if (_isString(item[patternKey])) {
                        item[key] = item[patternKey].replace(/__ID__/g, itemId);
                    }
                });

                // TODO access check

                // Check visible
                if (item.visible === false) {
                    return null;
                }
                if (item.id === actionId) {
                    return null;
                }
                if (item.actionType === 'item' && !itemId) {
                    return null;
                }

                return item;
            })
            .filter(Boolean);
    }

    _onDelete(itemId) {
        http.delete(null, {
            [this.props.primaryKey]: itemId
        }).then(() => {
            const actionId = _get(this.props, 'route.id');
            if (actionId === 'index') {
                this.props.dispatch(refresh(this.props.crudId));
            } else {
                this.props.dispatch(push('/'));
            }
        });
    }

}
