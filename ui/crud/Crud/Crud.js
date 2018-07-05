import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Router} from 'yii-steroids/ui/nav';
import _keyBy from 'lodash-es/keyBy';
import {push} from 'react-router-redux';

import {http, ui} from 'components';
import {refresh} from '../../../actions/list';
import Grid from '../../list/Grid';

@connect()
export default class Crud extends React.PureComponent {

    static propTypes = {
        crudId: PropTypes.string.isRequired,
        controls: PropTypes.array,
        listProps: PropTypes.object,
        formProps: PropTypes.object,
        className: PropTypes.string,
        formView: PropTypes.func,
        wrapperView: PropTypes.func,
        primaryKey: PropTypes.string,
    };

    static defaultProps = {
        primaryKey: PropTypes.stirng,
    };

    render() {
        const controls = _keyBy(this.props.controls, 'id');

        const CrudWrapperView = this.props.wrapperView || ui.getView('crud.CrudWrapperView');
        const CrudFormView = this.props.formView || ui.getView('crud.CrudFormView');
        return (
            <Router
                wrapperView={CrudWrapperView}
                wrapperProps={{
                    ...this.props,
                    onDelete: (itemId) => this._delete(itemId).then(() => this.props.dispatch(push('/'))),
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
                            ...this.props.listProps,
                            actions: (item, primaryKey) => ([
                                {
                                    id: 'view',
                                    url: null,
                                    to: `/${item[primaryKey]}/view`,
                                    visible: !!controls.view,
                                    ...controls.view,
                                },
                                {
                                    id: 'update',
                                    url: null,
                                    to: `/${item[primaryKey]}/update`,
                                    visible: !!controls.update,
                                    ...controls.update,
                                },
                                {
                                    id: 'delete',
                                    url: null,
                                    visible: !!controls.delete,
                                    ...controls.delete,
                                    onClick: () => this._delete(item[primaryKey]).then(() => this.props.dispatch(refresh(this.props.crudId))),
                                },
                            ]),
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

    _delete(itemId) {
        return http.delete(null, {
            [this.props.primaryKey]: itemId
        });
    }

}