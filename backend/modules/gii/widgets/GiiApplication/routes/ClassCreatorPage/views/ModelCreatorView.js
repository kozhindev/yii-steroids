import React from 'react';
import PropTypes from 'prop-types';
import {Form, Button, Field, DropDownField, FieldList} from 'yii-steroids/ui/form';

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

export default class ModelCreatorView extends React.PureComponent {

    static propTypes = {
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
                            <Field attribute='moduleId'/>
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
                                <Field attribute='queryModel'/>
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
                                component: DropDownField,
                                items: this.props.appTypes && this.props.appTypes.map(appType => ({
                                    id: appType.name,
                                    label: appType.title,
                                })),
                            },
                            {
                                attribute: 'isRequired',
                                firstLine: true,
                                headerClassName: 'd-none',
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
