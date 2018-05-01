import React from 'react';
import PropTypes from 'prop-types';
import {Form, Button, Field} from 'yii-steroids/frontend/ui/form';

import {html} from 'components';
import WidgetEntityMeta from '../../../../../forms/meta/WidgetEntityMeta';

import './WidgetCreatorView.scss';

const bem = html.bem('WidgetCreatorView');
const FORM_ID = 'WidgetCreatorView';

export default class WidgetCreatorView extends React.PureComponent {

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
                    model={WidgetEntityMeta}
                    layout='default'
                    initialValues={this.props.initialValues}
                    onComplete={this.props.onEntityComplete}
                >
                    <div className='row'>
                        <div className='col-3'>
                            <Field attribute='moduleId'/>
                        </div>
                        <div className='col-3'>
                            <Field attribute='name'/>
                        </div>
                    </div>
                    <Field attribute='parentName'/>
                    <h3 className='mt-4'>
                        Template
                    </h3>
                    <Field attribute='withPropTypes'/>
                    <Field attribute='withConnect'/>
                    <Field attribute='withGrid'/>
                    <Field attribute='withForm'/>
                    <Field attribute='withRouter'/>
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
