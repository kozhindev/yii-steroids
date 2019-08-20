import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {getFormValues, change} from 'redux-form';
import {Form, AutoCompleteField, Button, Field, DropDownField, FieldList} from '../../../../../../../../ui/form';
import _get from 'lodash/get';
import _some from 'lodash/some';

import {html} from 'components';
import RelationTypeMeta from '../../../../../enums/meta/RelationTypeMeta';
import ModelEntityMeta from '../../../../../forms/meta/ModelEntityMeta';
import ModelAttributeEntityMeta from '../../../../../forms/meta/ModelAttributeEntityMeta';
import ModelRelationEntityMeta from '../../../../../forms/meta/ModelRelationEntityMeta';
import ClassTypeMeta from '../../../../../enums/meta/ClassTypeMeta';
import ModelAttributeRow from './ModelAttributeRow';
import ModelRelationRow from './ModelRelationRow';
import MigrateModeMeta from '../../../../../enums/meta/MigrateModeMeta';

import './ModelCreatorView.scss';

const bem = html.bem('ModelCreatorView');
const FORM_ID = 'ModelCreatorView';

@connect(
    state => ({
        formValues: getFormValues(FORM_ID)(state),
    })
)
export default class ModelCreatorView extends React.PureComponent {

    static propTypes = {
        moduleIds: PropTypes.arrayOf(PropTypes.string),
        entity: PropTypes.shape({
            moduleId: PropTypes.string,
            name: PropTypes.string,
            className: PropTypes.string,
        }),
        initialValues: PropTypes.object,
        appTypes: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string,
            title: PropTypes.string,
            additionalFields: PropTypes.arrayOf(PropTypes.shape({
                attribute: PropTypes.string,
                component: PropTypes.string,
                label: PropTypes.string,
            })),
        })),
        classType: PropTypes.string,
        onEntityComplete: PropTypes.func,
        classes: PropTypes.shape({
            model: PropTypes.arrayOf(PropTypes.shape({
                moduleId: PropTypes.string,
                name: PropTypes.string,
                className: PropTypes.string,
                tableName: PropTypes.string,
            })),
            form: PropTypes.arrayOf(PropTypes.shape({
                moduleId: PropTypes.string,
                name: PropTypes.string,
                className: PropTypes.string,
            })),
            'enum': PropTypes.arrayOf(PropTypes.shape({
                moduleId: PropTypes.string,
                name: PropTypes.string,
                className: PropTypes.string,
            })),
        }),
        sampleAttributes: PropTypes.arrayOf(PropTypes.shape({
            appType: PropTypes.string,
            name: PropTypes.string,
            defaultValue: PropTypes.string,
            example: PropTypes.string,
            hint: PropTypes.string,
            label: PropTypes.string,
        })),
        formValues: PropTypes.object,
    };

    render() {
        return (
            <div className={bem.block()}>
                <Form
                    formId={FORM_ID}
                    action='/api/gii/class-save'
                    model={ModelEntityMeta}
                    layout='default'
                    size='sm'
                    initialValues={this.props.initialValues}
                    onComplete={this.props.onEntityComplete}
                >
                    <div className='row'>
                        <div className='col-3'>
                            <Field
                                attribute='moduleId'
                                items={this.props.moduleIds}
                                component={AutoCompleteField}
                            />
                        </div>
                        <div className='col-4'>
                            <Field attribute='name'/>
                        </div>
                        {this.props.classType === ClassTypeMeta.MODEL && (
                            <div className='col-4'>
                                <Field attribute='tableName'/>
                            </div>
                        )}
                    </div>
                    {this.props.classType === ClassTypeMeta.FORM && (
                        <div className='row'>
                            <div className='col-4'>
                                <Field
                                    attribute='queryModel'
                                    component={AutoCompleteField}
                                    items={_get(this.props, 'classes.model', []).map(item => item.className)}
                                />
                            </div>
                        </div>
                    )}
                    <h3 className='mt-4'>
                        Attributes
                    </h3>
                    <FieldList
                        attribute='attributeItems'
                        model={ModelAttributeEntityMeta}
                        itemView={ModelAttributeRow}
                        appTypes={this.props.appTypes}
                        className={bem(bem.element('field-list'), 'my-2')}
                        items={[
                            {
                                attribute: 'name',
                                placeholder: 'Attribute',
                                className: bem.element('input-attribute'),
                                firstLine: true,
                                component: AutoCompleteField,
                                items: this.props.sampleAttributes,
                                onSelect: (item, params) => {
                                    const hasFilled = _some(Object.keys(item.params), key => !!_get(this.props.formValues, params.prefix + '.' + key));
                                    if (!hasFilled) {
                                        this.props.dispatch(Object.keys(item.params).map(key => {
                                            return change(FORM_ID, params.prefix + '.' + key, item.params[key]);
                                        }));
                                    }
                                },

                            },
                            {
                                attribute: 'label',
                                label: 'Label / Hint',
                                placeholder: 'Label',
                                className: bem.element('input-label'),
                                firstLine: true,
                            },
                            {
                                attribute: 'hint',
                                placeholder: 'Hint',
                                className: bem.element('input-hint'),
                                headerClassName: 'd-none',
                            },
                            {
                                attribute: 'example',
                                placeholder: 'Example value',
                                className: bem.element('input-example-value'),
                                headerClassName: 'd-none',
                            },
                            {
                                attribute: 'defaultValue',
                                label: 'Default / Example',
                                placeholder: 'Default value',
                                className: bem.element('input-default-value'),
                                firstLine: true,
                            },
                            {
                                attribute: 'appType',
                                firstLine: true,
                                className: bem.element('input-app-type'),
                                component: AutoCompleteField,
                                items: (this.props.appTypes || []).map(item => item.name),
                            },
                            {
                                attribute: 'isRequired',
                                firstLine: true,
                                headerClassName: 'd-none',
                            },
                            {
                                attribute: 'isSortable',
                                firstLine: true,
                                headerClassName: 'd-none',
                                visible: this.props.classType === ClassTypeMeta.FORM,
                            },
                            {
                                attribute: 'isPublishToFrontend',
                                firstLine: true,
                                headerClassName: 'd-none',
                                visible: this.props.classType === ClassTypeMeta.MODEL,
                            },
                        ]}
                    />
                    <div>
                        <h3 className='mt-4'>
                            Relations
                        </h3>
                        <FieldList
                            attribute='relationItems'
                            model={ModelRelationEntityMeta}
                            itemView={ModelRelationRow}
                            className={bem(bem.element('field-list'), 'my-2')}
                            initialRowsCount={0}
                            items={[
                                {
                                    attribute: 'type',
                                    component: DropDownField,
                                    items: RelationTypeMeta,
                                },
                                {
                                    attribute: 'name',
                                },
                                {
                                    attribute: 'relationModel',
                                    items: _get(this.props, 'classes.model', []).map(item => item.className),
                                    component: AutoCompleteField,
                                },
                                {
                                    attribute: 'relationKey',
                                },
                                {
                                    attribute: 'selfKey',
                                },
                                {
                                    attribute: 'viaTable',
                                    placeholder: 'Junction table',
                                    headerClassName: 'd-none',
                                    isVia: true,
                                },
                                {
                                    attribute: 'viaRelationKey',
                                    placeholder: 'Relation key',
                                    headerClassName: 'd-none',
                                    isVia: true,
                                },
                                {
                                    attribute: 'viaSelfKey',
                                    placeholder: 'Self key',
                                    headerClassName: 'd-none',
                                    isVia: true,
                                },
                            ]}
                        />
                    </div>
                    <div className='mt-4 row'>
                        <div className='col-md-3'>
                            <Field
                                attribute='migrateMode'
                                items={MigrateModeMeta}
                            />
                        </div>
                    </div>
                    <div className='mb-5'>
                        <Button
                            type='submit'
                            label='Save'
                        />
                    </div>
                </Form>
            </div>
        );
    }

}
