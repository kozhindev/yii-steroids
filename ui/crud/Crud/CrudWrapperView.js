import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import _keyBy from 'lodash-es/keyBy';

import {html, locale} from 'components';
import Nav from '../../nav/Nav';

const bem = html.bem('CrudWrapperView');

export default class CrudWrapperView extends React.Component {

    static propTypes = {
        controls: PropTypes.arrayOf(PropTypes.object),
        route: PropTypes.shape({
            id: PropTypes.string,
            isExact: PropTypes.bool,
            params: PropTypes.object,
            path: PropTypes.string,
            url: PropTypes.string,
        }),
        onDelete: PropTypes.func,
    };

    render() {
        const actionId = _get(this.props, 'route.id');
        const itemId = _get(this.props, 'route.params.id');
        const controls = _keyBy(this.props.controls, 'id');
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <div className={bem(bem.element('controls'), 'd-flex justify-content-between')}>
                    <Nav
                        layout='button'
                        items={[
                            {
                                id: 'create',
                                icon: 'add_circle',
                                label: locale.t('Добавить'),
                                to: '/create',
                                color: 'success',
                                outline: true,
                                visible: actionId === 'index' && !!controls.create,
                                ...controls.create,
                            },
                            {
                                id: 'index',
                                icon: 'keyboard_arrow_left',
                                label: locale.t('К списку'),
                                to: '/',
                                color: 'secondary',
                                outline: true,
                                visible: actionId !== 'index',
                                ...controls.index,
                            },
                            {
                                id: 'view',
                                label: locale.t('Просмотр'),
                                to: `/${itemId}`,
                                color: 'secondary',
                                outline: true,
                                visible: actionId === 'update' && !!controls.view,
                                ...controls.view,
                            },
                            {
                                id: 'update',
                                label: locale.t('Редактировать'),
                                to: `/${itemId}/update`,
                                color: 'secondary',
                                outline: true,
                                visible: actionId === 'view' && !!controls.update,
                                ...controls.update,
                            },
                            {
                                id: 'delete',
                                icon: 'delete',
                                label: locale.t('Удалить'),
                                confirm: locale.t('Удалить запись?'),
                                color: 'danger',
                                outline: true,
                                visible: actionId !== 'index' && actionId !== 'create' && !!controls.delete,
                                ...controls.delete,
                                onClick: () => this.props.onDelete(itemId),
                            },
                        ]}
                    />
                </div>
                {this.props.children}
            </div>
        );
    }

    renderItems(position) {
        return (
            <Nav
                layout='button'
                items={this.props.controls.filter(item => item.position === position)}
            />
        );
    }

}