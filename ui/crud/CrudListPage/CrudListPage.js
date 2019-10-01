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
class CrudListPage extends React.PureComponent {

    static propTypes = {
        editMode: PropTypes.oneOf(['page', 'modal']),
        crudId: PropTypes.string,
        restUrl: PropTypes.string,
        controls: PropTypes.array,
        route: PropTypes.shape({
            id: PropTypes.string,
            isExact: PropTypes.bool,
            params: PropTypes.object,
            path: PropTypes.string,
            url: PropTypes.string,
        }),
        grid: PropTypes.object,
        view: PropTypes.func,
    };

    static defaultProps = {
        primaryKey: 'id',
        controls: [
            {
                id: 'create',
            },
        ]
    };

    constructor() {
        super(...arguments);

        this._actionsHandler = this._actionsHandler.bind(this);
    }

    render() {
        const crudId = this.props.crudId || this.props.route.id;

        // Crud controls
        const defaultControls = {
            create: {
                to: this.props.route.path,
            },
        };
        const controls = (this.props.controls || []).map(item => ({
            ...defaultControls[item.id],
            ...item,
        }));

        const CrudListPageView = this.props.wrapperView || ui.getView('crud.CrudListPageView');
        return (
            <CrudListPageView
                {...this.props}
                grid={{
                    listId: crudId,
                    primaryKey: this.props.primaryKey,
                    emptyText: __('Нет записей'),
                    ...this.props.grid,
                    actions: this._actionsHandler
                }}
            />
        );
    }

    _actionsHandler(item, primaryKey) {
        const itemId = item[primaryKey];
        const defaultActions = {
            view: {
                to: `${this.props.route.path}/${itemId}`,
            },
            update: {
                to: `${this.props.route.path}/${itemId}/update`,
            },
            delete: {
                onClick: () => this._onDelete(itemId),
            },
        };
        return (this.props.grid && this.props.grid.actions || []).map(action => ({
            ...defaultActions[item.id],
            ...action,
        }));
    }

    _onDelete(itemId) {
        http.delete(`${this.props.restUrl}/${itemId}`).then(() => {
            /*const actionId = _get(this.props, 'route.id');
            if (actionId === 'index') {
                this.props.dispatch(refresh(this.props.crudId));
            } else {
                this.props.dispatch(push('/'));
            }*/
        });
    }

}
