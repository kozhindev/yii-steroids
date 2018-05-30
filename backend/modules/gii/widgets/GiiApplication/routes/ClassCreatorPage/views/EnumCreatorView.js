import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {formValueSelector} from 'redux-form';
import _get from 'lodash-es/get';
import _upperFirst from 'lodash-es/upperFirst';
import {Form, Button, Field, InputField, CheckboxField, FieldList} from 'yii-steroids/frontend/ui/form';

import {html} from 'components';
import EnumEntityMeta from '../../../../../forms/meta/EnumEntityMeta';
import EnumItemEntityMeta from '../../../../../forms/meta/EnumItemEntityMeta';

import './EnumCreatorView.scss';

const bem = html.bem('EnumCreatorView');
const FORM_ID = 'EnumCreatorView';

let selector = null;

@connect(
    state => {
        if (!selector) {
            selector = formValueSelector(FORM_ID);
        }
        const values = selector(state, 'isCustomValues', 'items');
        return {
            isCustomValues: !!values.isCustomValues,
            hasEnumValues: !!(values.items || []).find(item => item && item.value),
        };
    }
)
export default class EnumCreatorView extends React.PureComponent {

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
        isCustomValues: PropTypes.bool,
        hasEnumValues: PropTypes.bool,
        onEntityComplete: PropTypes.func,
    };

    render() {
        return (
            <div className={bem.block()}>
                <Form
                    formId={FORM_ID}
                    action='/api/gii/class-save'
                    model={EnumEntityMeta}
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
                    </div>
                    <div className='mt-2'>
                        <CheckboxField
                            attribute='isCustomValues'
                            label='Show Enum values'
                            disabled={this.props.hasEnumValues}
                        />
                    </div>
                    <h3 className='mt-4'>
                        Items
                    </h3>
                    <FieldList
                        attribute='items'
                        model={EnumItemEntityMeta}
                        className={bem(bem.element('field-list'), 'my-2')}
                        items={[]
                            .concat([
                                {
                                    attribute: 'name',
                                    className: bem.element('input-attribute'),
                                },
                                {
                                    attribute: 'value',
                                    visible: this.props.isCustomValues || this.props.hasEnumValues,
                                },
                                {
                                    attribute: 'label',
                                },
                                {
                                    attribute: 'cssClass',
                                },
                            ])
                            .concat(_get(this.props.entity, 'customColumns', []).map(attribute => ({
                                attribute: 'custom.' + attribute,
                                label: _upperFirst(attribute),
                                component: InputField,
                            })))
                        }
                    />
                    <div className='mt-4'>
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
