import React from 'react';
import PropTypes from 'prop-types';
import {Form, Button, Field, FieldList} from '../../../../../../../../ui/form';

import {html} from 'components';
import CrudEntityMeta from '../../../../../forms/meta/CrudEntityMeta';
import CrudItemEntityMeta from '../../../../../forms/meta/CrudItemEntityMeta';

import './CrudCreatorView.scss';

const bem = html.bem('CrudCreatorView');
const FORM_ID = 'CrudCreatorView';

export default class CrudCreatorView extends React.PureComponent {

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
                    model={CrudEntityMeta}
                    layout='default'
                    size='sm'
                    initialValues={this.props.initialValues}
                    onComplete={this.props.onEntityComplete}
                >
                    <div className='row'>
                        <div className='col-3'>
                            <Field attribute='moduleId'/>
                        </div>
                        <div className='col-3'>
                            <Field
                                attribute='name'
                                placeholder='UsersController'
                            />
                        </div>
                    </div>
                    <div className='row'>
                        <div className='col-5'>
                            <Field
                                attribute='queryModel'
                                placeholder='app\user\models\User'
                            />
                        </div>
                        <div className='col-5'>
                            <Field
                                attribute='searchModel'
                                placeholder='app\user\forms\UserSearch'
                            />
                        </div>
                    </div>
                    <div className='row'>
                        <div className='col-5'>
                            <Field
                                attribute='title'
                                placeholder='Поиск пользователей'
                            />
                        </div>
                        <div className='col-3'>
                            <Field
                                attribute='url'
                                placeholder='/users'
                            />
                        </div>
                    </div>
                    <h3 className='mt-4'>
                        Attributes
                    </h3>
                    <FieldList
                        attribute='items'
                        model={CrudItemEntityMeta}
                        appTypes={this.props.appTypes}
                        className={bem(bem.element('field-list'), 'my-2')}
                        items={[
                            {
                                attribute: 'name',
                            },
                            {
                                attribute: 'showInForm',
                            },
                            {
                                attribute: 'showInTable',
                            },
                            {
                                attribute: 'showInView',
                            },
                        ]}
                    />
                    <Field attribute='createActionIndex'/>
                    <Field attribute='withDelete'/>
                    <Field attribute='withSearch'/>
                    <Field attribute='createActionCreate'/>
                    <Field attribute='createActionUpdate'/>
                    <Field attribute='createActionView'/>
                    <div className='mt-4 mb-5'>
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
