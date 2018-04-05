import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash/get';
import _find from 'lodash/find';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm, getFormValues, initialize, change} from 'redux-form';
import _upperFirst from 'lodash/upperFirst';

import {widget} from 'components';
import ModelAttributesArrayField from './views/ModelAttributesArrayField';

const FORM_ID = 'CrudForm';
const selectorFormValues = getFormValues(FORM_ID);

@widget.register('\\steroids\\modules\\gii\\widgets\\CrudForm\\CrudForm')
@connect(
    state => ({
        formValues: selectorFormValues(state),
    })
)
@reduxForm({
    form: FORM_ID,
})
export default class CrudForm extends React.Component {

    static propTypes = {
        modules: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.string,
            className: PropTypes.string,
        })),
        models: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string,
            module: PropTypes.string,
            className: PropTypes.string,
        })),
        formModels: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string,
            module: PropTypes.string,
            className: PropTypes.string,
        })),
        controllers: PropTypes.arrayOf(PropTypes.shape({
            className: PropTypes.string,
            name: PropTypes.string,
            moduleClass: PropTypes.shape({
                id: PropTypes.string,
                className: PropTypes.string,
            }),
            metaClass: PropTypes.shape({
                className: PropTypes.string,
                meta: PropTypes.object,
            }),
        })),
        formValues: PropTypes.object,
        csrfToken: PropTypes.string,
    };

    static defaultMeta = {
        createActionIndex: true,
        withDelete: true,
        createActionCreate: true,
        createActionUpdate: true,
        createActionView: true,
    };

    componentWillReceiveProps(nextProps) {
        const prevFormValues = this.props.formValues || {};
        const nextFormValues = nextProps.formValues || {};

        if ((!prevFormValues.moduleId || !prevFormValues.controllerName)
            && nextFormValues.moduleId && nextFormValues.controllerName) {

            const controller = this.props.controllers.find(c => c.moduleClass.id === nextFormValues.moduleId && c.name === nextFormValues.controllerName);
            const values = {
                ...nextFormValues,
                meta: controller ? controller.metaClass.meta : CrudForm.defaultMeta,
            };

            this.props.dispatch(initialize(FORM_ID, values));
        }

        if (_get(prevFormValues, 'meta.modelClassName') !== _get(nextFormValues, 'meta.modelClassName')) {
            const model = this.props.models.find(m => m.className === _get(nextFormValues, 'meta.modelClassName'));
            const attributes = [];
            if (model.metaClass.meta) {
                model.metaClass.meta.forEach(item => {
                    attributes.push({
                        name: item.name,
                        ..._find(_get(nextFormValues, 'meta.modelAttributes'), i => i.name === item.name),
                    });
                    item.items.forEach(item2 => {
                        attributes.push({
                            name: item2.name,
                            ..._find(_get(nextFormValues, 'meta.modelAttributes'), i => i.name === item2.name),
                        });
                    });
                });
            }
            this.props.dispatch(change(FORM_ID, 'meta.modelAttributes', attributes));
        }

        if (_get(prevFormValues, 'meta.formModelClassName') !== _get(nextFormValues, 'meta.formModelClassName')) {
            const model = this.props.formModels.find(m => m.className === _get(nextFormValues, 'meta.formModelClassName'));
            const attributes = model
                ? model.metaClass.meta.map(item => ({
                    name: item.name,
                    ..._find(_get(nextFormValues, 'meta.formModelAttributes'), i => i.name === item.name),
                }))
                : [];
            this.props.dispatch(change(FORM_ID, 'meta.formModelAttributes', attributes));
        }
    }

    render() {
        const formValues = this.props.formValues || {};
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
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <Field
                            name='moduleId'
                            component='input'
                            list={`${FORM_ID}_moduleIdList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_moduleIdList`}>
                            {this.props.modules.map(module => (
                                <option key={module.id} value={module.id}/>
                            ))}
                        </datalist>
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Controller Name
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6 form-inline'>
                        <Field
                            name='controllerName'
                            component='input'
                            className='form-control'
                            style={{
                                width: 250,
                            }}
                        />
                        &nbsp;&nbsp;&nbsp;
                        {formValues.moduleId && formValues.controllerName && (
                            <span className='text-muted'>
                                app\
                                {formValues.moduleId.replace(/\./g, '\\')}
                                \controller\
                                {_upperFirst(formValues.controllerName)}
                                Controller
                            </span>
                        )}
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Model
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <Field
                            name='meta[modelClassName]'
                            component='input'
                            list={`${FORM_ID}_modelNameList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_modelNameList`}>
                            {this.props.models.map(model => (
                                <option key={model.className} value={model.className}/>
                            ))}
                        </datalist>
                        {formValues.meta && formValues.meta.modelClassName && (
                            <FieldArray
                                name='meta[modelAttributes]'
                                component={ModelAttributesArrayField}
                                keys={['showInForm', 'showInTable', 'showInView']}
                            />
                        )}
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Form Model
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <Field
                            name='meta[formModelClassName]'
                            component='input'
                            list={`${FORM_ID}_formModelNameList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_formModelNameList`}>
                            {this.props.formModels.map(model => (
                                <option key={model.className} value={model.className}/>
                            ))}
                        </datalist>
                        {formValues.meta && formValues.meta.formModelClassName && (
                            <FieldArray
                                name='meta[formModelAttributes]'
                                component={ModelAttributesArrayField}
                                keys={['showInFilter']}
                            />
                        )}
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Url to index
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <Field
                            name='meta[url]'
                            component='input'
                            className='form-control'
                            placeholder='profile/orders'
                        />
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Access roles
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <Field
                            name='meta[roles]'
                            component='input'
                            className='form-control'
                            placeholder='*'
                            list={`${FORM_ID}_rolesList`}
                        />
                        <datalist id={`${FORM_ID}_rolesList`}>
                            <option value='@'/>
                            <option value='admin'/>
                        </datalist>
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Title
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <Field
                            name='meta[title]'
                            component='input'
                            className='form-control'
                            placeholder='Список пользователей'
                        />
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Actions
                    </label>
                    <div className='col-sm-10 col-md-8 col-lg-6'>
                        <div className='checkbox'>
                            <label>
                                <Field
                                    name='meta[createActionIndex]'
                                    component='input'
                                    type='checkbox'
                                />
                                &nbsp;
                                Index
                            </label>
                        </div>
                        <div className='checkbox' style={{marginLeft: '15px'}}>
                            <label>
                                <Field
                                    name='meta[withDelete]'
                                    component='input'
                                    type='checkbox'
                                />
                                &nbsp;
                                with delete action
                            </label>
                        </div>
                        <div className='checkbox' style={{marginLeft: '15px'}}>
                            <label>
                                <Field
                                    name='meta[withSearch]'
                                    component='input'
                                    type='checkbox'
                                />
                                &nbsp;
                                with search form
                            </label>
                        </div>
                        <div className='checkbox'>
                            <label>
                                <Field
                                    name='meta[createActionCreate]'
                                    component='input'
                                    type='checkbox'
                                />
                                &nbsp;
                                Create
                            </label>
                        </div>
                        <div className='checkbox'>
                            <label>
                                <Field
                                    name='meta[createActionUpdate]'
                                    component='input'
                                    type='checkbox'
                                />
                                &nbsp;
                                Update
                            </label>
                        </div>
                        <div className='checkbox'>
                            <label>
                                <Field
                                    name='meta[createActionView]'
                                    component='input'
                                    type='checkbox'
                                />
                                &nbsp;
                                View
                            </label>
                        </div>
                    </div>
                </div>
                <div className='form-group'>
                    <div className='col-sm-offset-2 col-sm-10'>
                        <button
                            type='submit'
                            className='btn btn-success'
                        >
                            Сохранить
                        </button>
                    </div>
                </div>
            </form>
        );
    }

}