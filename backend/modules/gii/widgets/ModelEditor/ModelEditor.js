import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm, formValueSelector, initialize, arrayPush} from 'redux-form';
import _isArray from 'lodash/isArray';

import {widget} from 'components';
import tableNavigationHandler from './tableNavigationHandler';
import ModelMetaArrayField from './views/ModelMetaArrayField';
import ModelRelationsArrayField from './views/ModelRelationsArrayField';

const FORM_ID = 'ModelEditor';
const selector = formValueSelector(FORM_ID);

@widget.register('\\steroids\\modules\\gii\\widgets\\ModelEditor\\ModelEditor')
@connect(
    state => ({
        formValues: {
            meta: selector(state, 'meta') || [],
            relations: selector(state, 'relations') || [],
            moduleId: selector(state, 'moduleId'),
            modelName: selector(state, 'modelName'),
        }
    })
)
@reduxForm({
    form: FORM_ID,
})
export default class ModelEditor extends React.Component {

    static formId = 'ModelEditor';

    static propTypes = {
        modules: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.string,
            className: PropTypes.string,
        })),
        models: PropTypes.arrayOf(PropTypes.shape({
            className: PropTypes.string,
            name: PropTypes.string,
            moduleClass: PropTypes.shape({
                id: PropTypes.string,
                className: PropTypes.string,
            }),
            metaClass: PropTypes.shape({
                className: PropTypes.string,
                meta: PropTypes.array,
                relations: PropTypes.array,
            }),
        })),
        tableNames: PropTypes.array,
        appTypes: PropTypes.array,
        formValues: PropTypes.object,
        csrfToken: PropTypes.string,
    };

    static defaultMeta = [
        {
            name: 'id',
            label: 'ID',
            appType: 'primaryKey',
        },
    ];

    constructor() {
        super(...arguments);

        this._onTableKeyDown = this._onTableKeyDown.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        if ((!this.props.formValues.moduleId || !this.props.formValues.modelName)
            && nextProps.formValues.moduleId && nextProps.formValues.modelName) {

            const model = this.getModel(nextProps);
            const values = {
                ...nextProps.formValues,
                meta: model ? model.metaClass.meta : ModelEditor.defaultMeta,
                relations: model ? model.metaClass.relations : [],
            };
            if (model) {
                values.tableName = model.tableName;
            }

            this.props.dispatch(initialize(FORM_ID, values));
        }
    }

    getModel(props) {
        props = props || this.props;
        return props.models.find(m => m.moduleClass.id === props.formValues.moduleId && m.name === props.formValues.modelName);
    }

    render() {
        return (
            <form
                method='post'
                className='form-horizontal'
            >
                <input
                    type='hidden'
                    name='_csrf'
                    value={this.props.csrfToken}
                />
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Module
                    </label>
                    <div className='col-sm-8'>
                        <Field
                            name='moduleId'
                            component='input'
                            list={`${FORM_ID}_moduleIdList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_moduleIdList`}>
                            {this.props.modules.map(module => (
                                <option key={module.id} value={module.id} />
                            ))}
                        </datalist>
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Model
                    </label>
                    <div className='col-sm-8'>
                        <Field
                            name='modelName'
                            component='input'
                            list={`${FORM_ID}_modelNameList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_modelNameList`}>
                            {this.props.models.filter(model => model.moduleClass.id === this.props.formValues.moduleId).map(model => (
                                <option key={model.name} value={model.name} />
                            ))}
                        </datalist>
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Table name
                    </label>
                    <div className='col-sm-8'>
                        <Field
                            name='tableName'
                            component='input'
                            list={`${FORM_ID}_tableNameList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_tableNameList`}>
                            {this.props.tableNames.map(name => (
                                <option key={name} value={name} />
                            ))}
                        </datalist>
                    </div>
                </div>
                <datalist id={`${FORM_ID}_selfAttributesList`}>
                    {this._getAttributes().map(name => (
                        <option key={name} value={name} />
                    ))}
                </datalist>
                {this._renderDataLists()}
                {this.props.formValues.moduleId && this.props.formValues.modelName && (
                    <FieldArray
                        name='meta'
                        component={ModelMetaArrayField}
                        appTypes={this.props.appTypes}
                        onKeyDown={e => this._onTableKeyDown(e, 'meta')}
                        isAR
                    />
                )}
                {this.props.formValues.moduleId && this.props.formValues.modelName && (
                    <FieldArray
                        name='relations'
                        component={ModelRelationsArrayField}
                        models={this.props.models}
                        onKeyDown={e => this._onTableKeyDown(e, 'relations')}
                    />
                )}
                <div>
                    <h3>
                        Migrations
                    </h3>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Migration mode
                    </label>
                    <div className='col-sm-8'>
                        <Field
                            name='migrateMode'
                            component='select'
                            className='form-control'
                        >
                            <option value='update'>Update (diff)</option>
                            <option value='create'>Create table</option>
                            <option value='none'>No migrate</option>
                        </Field>
                    </div>
                </div>
                <div className='form-group'>
                    <div className='col-sm-offset-2 col-sm-6'>
                        <button
                            type='submit'
                            className='btn btn-success'
                        >
                            {this.getModel() ? 'Обновить модель' : 'Создать модель'}
                        </button>
                    </div>
                </div>
            </form>
        );
    }

    _renderDataLists() {
        const lists = {};
        this.props.appTypes.forEach(appType => {
            Object.keys(appType.fieldProps).map(key => {
                const list = appType.fieldProps[key].list;
                switch (list) {
                    case 'types':
                        lists[key] = this.props.appTypes.map(r => r && r.name).filter(Boolean);
                        break;

                    case 'relations':
                        lists[key] = this.props.formValues.relations.map(r => r && r.name).filter(Boolean);
                        break;

                    case 'attributes':
                        lists[key] = this._getAttributes();
                        break;

                    default:
                        if (_isArray(list)) {
                            lists[key] = list;
                        }
                }
            });
        });

        return Object.keys(lists).map(key => (
            <datalist key={key} id={`${ModelMetaArrayField.formId}_${key}`}>
                {lists[key].map(item => (
                    <option key={item} value={item} />
                ))}
            </datalist>
        ));
    }

    _getAttributes() {
        const names = [];
        this.props.formValues.meta.forEach(item => {
            if (item && item.name) {
                names.push(item.name);

                (item.items || []).forEach(subItem => {
                    if (subItem && subItem.name) {
                        names.push(subItem.name);
                    }
                });
            }
        });
        return names;
    }

    _onTableKeyDown(e, name) {
        tableNavigationHandler(e, () => this.props.dispatch(arrayPush(ModelMetaArrayField.formId, name)));
    }

}
