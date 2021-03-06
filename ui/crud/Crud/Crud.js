import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash/get';

import {http, ui} from 'components';
import {getCurrentRoute} from '../../../reducers/navigation';
import {goToPage} from '../../../actions/navigation';
import {refresh} from '../../../actions/list';
import Grid from '../../list/Grid';
import Form from '../../form/Form';
import {showNotification} from '../../../actions/notifications';

const getCrudId = props => props.crudId || props.baseRouteId;

@connect(
    state => ({
        route: getCurrentRoute(state),
    })
)
export default class Crud extends React.PureComponent {

    static propTypes = {
        editMode: PropTypes.oneOf(['page', 'modal']),
        crudId: PropTypes.string,
        baseRouteId: PropTypes.string,
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
        form: PropTypes.object,
        view: PropTypes.elementType,
        gridView: PropTypes.elementType,
        formView: PropTypes.elementType,
        detailView: PropTypes.elementType,
        refreshGridAfterDelete: PropTypes.bool,
    };

    static defaultProps = {
        primaryKey: 'id',
        refreshGridAfterDelete: true,
    };

    constructor() {
        super(...arguments);

        this._actionsHandler = this._actionsHandler.bind(this);
    }

    render() {
        const defaultControls = {
            index: {
                visible: true,
                toRoute: this.props.baseRouteId,
            },
            view: {
                visible: false,
            },
            create: {
                visible: true,
                toRoute: this.props.baseRouteId + '_create',
            },
            update: {
                visible: false,
            },
            delete: {
                visible: false,
                position: 'right',
                onClick: async () => {
                    await http.delete(`${this.props.restUrl}/${this.props.route.params.id}`);
                    this.props.dispatch(goToPage(this.props.baseRouteId));
                }
            },
        };

        // Append default controls
        const controls = [].concat(this.props.controls || []);
        const controlsIds = controls.map(item => item.id).filter(Boolean);
        Object.keys(defaultControls).forEach(id => {
            if (!controlsIds.includes(id)) {
                controls.push({id});
            }
        });

        // Resolve content
        let content = null;
        switch (this.props.route.id) {
            case this.props.baseRouteId:
                content = this.renderGrid();
                defaultControls.index.visible = false;
                break;

            case this.props.baseRouteId + '_create':
                content = this.renderForm();
                defaultControls.create.visible = false;
                break;

            case this.props.baseRouteId + '_update':
                content = this.renderForm();
                defaultControls.create.visible = false;
                defaultControls.delete.visible = true;
                defaultControls.view = {
                    visible: !!this.props.detailView,
                    toRoute: this.props.baseRouteId + '_view',
                    toRouteParams: {
                        id: this.props.route.params.id,
                    },
                };
                break;

            case this.props.baseRouteId + '_view':
                content = this.renderDetail();
                defaultControls.create.visible = false;
                defaultControls.delete.visible = true;
                defaultControls.update = {
                    visible: true,
                    toRoute: this.props.baseRouteId + '_update',
                    toRouteParams: {
                        id: this.props.route.params.id,
                    },
                };
                break;
        }

        const CrudView = this.props.view || ui.getView('crud.CrudView');
        return (
            <CrudView
                {...this.props}
                controls={controls.map(item => ({
                    ...defaultControls[item.id],
                    ...item,
                }))}
            >
                {content}
            </CrudView>
        );
    }

    renderGrid() {
        const restUrl = this.props.restUrl
            ? this.props.restUrl + (this.props.restUrl.indexOf('?') !== -1 ? '&' : '?') + 'scope=model,permissions'
            : undefined;

        const GridComponent = this.props.gridView || Grid;
        return (
            <GridComponent
                listId={getCrudId(this.props)}
                action={restUrl}
                actionMethod='get'
                defaultPageSize={50}
                paginationSizeView={false}
                loadMore={false}
                primaryKey={this.props.primaryKey}
                emptyText={__('Нет записей')}
                columns={[this.props.primaryKey]}
                {...this.props.grid}
                actions={this._actionsHandler}
            />
        );
    }

    renderForm() {
        const FormComponent = this.props.formView || Form;
        return (
            <FormComponent
                formId={this._getFormId(this.props)}
                initialValues={this.props.item}
                action={this.props.restUrl + (this.props.route.params.id ? '/' + this.props.route.params.id : '')}
                autoFocus
                onComplete={() => {
                    window.scrollTo(0, 0);
                    this.props.dispatch(showNotification(__('Запись успешно обновлена.'), 'success'));
                }}
                {...this.props.form}
            />
        );
    }

    renderDetail() {
        const DetailView = this.props.detailView;
        if (DetailView) {
            return <DetailView />
        } else {
            return null;
        }
    }

    _actionsHandler(item, primaryKey) {
        const itemId = item[primaryKey];
        const defaultActions = {
            view: {
                visible: !!this.props.detailView,
                toRoute: this.props.baseRouteId + '_view',
                toRouteParams: {
                    id: itemId,
                },
            },
            update: {
                toRoute: this.props.baseRouteId + '_update',
                toRouteParams: {
                    id: itemId,
                },
            },
            delete: {
                onClick: async () => {
                    await http.delete(`${this.props.restUrl}/${itemId}`);
                    if (this.props.refreshGridAfterDelete) {
                        this.props.dispatch(refresh(getCrudId(this.props)))
                    }
                }
            },
        };

        // Append default actions
        const actions = [].concat(this.props.grid && this.props.grid.actions || []);
        const actionsIds = actions.map(item => item.id).filter(Boolean);
        Object.keys(defaultActions).forEach(id => {
            if (!actionsIds.includes(id)) {
                actions.push({id});
            }
        });

        return actions.map(action => ({
            ...defaultActions[action.id],
            ...action,
        }));
    }

    _getFormId(props) {
        let formId = getCrudId(this.props);
        if (_get(props, 'item.id')) {
            formId += '_' + props.item.id;
        }
        return formId;
    }
}
