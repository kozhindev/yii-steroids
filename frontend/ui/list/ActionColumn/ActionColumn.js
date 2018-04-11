import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import _has from 'lodash-es/has';
import _upperFirst from 'lodash-es/upperFirst';

import {locale} from 'components';
import Nav from '../../Nav';

export default class ActionColumn extends React.PureComponent {

    static propTypes = {
        primaryKey: PropTypes.string,
        actions: PropTypes.arrayOf(PropTypes.object),
        item: PropTypes.object,
    };

    render() {
        const id = _get(this.props.item, this.props.primaryKey);
        const defaultActions = {
            view: {
                rule: 'view',
                icon: 'visibility',
                label: locale.t('Просмотреть'),
                url: location.pathname + `/view/${id}`,
            },
            update: {
                rule: 'update',
                icon: 'mode_edit',
                label: locale.t('Редактировать'),
                url: location.pathname + `/update/${id}`,
            },
            delete: {
                rule: 'delete',
                icon: 'delete',
                label: locale.t('Удалить'),
                url: location.pathname + `/delete/${id}`,
            },
        };

        return (
            <Nav
                {...this.props}
                layout='icon'
                items={this.props.actions.map(action => ({
                    ...defaultActions[action.id],
                    ...action,
                    visible: !!this.props.item['can' + _upperFirst(action.id)],
                }))}
            />
        );
    }

}