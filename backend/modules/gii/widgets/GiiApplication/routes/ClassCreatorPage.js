import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import {Form, FieldList} from 'yii-steroids/frontend/ui/form';

import {html} from 'components';
import ClassCreatorFormMeta from '../../../forms/meta/ClassCreatorFormMeta';
import ClassCreatorAttributeFormMeta from '../../../forms/meta/ClassCreatorAttributeFormMeta';

import './ClassCreatorPage.scss';

const bem = html.bem('ClassCreatorPage');

export default class ClassCreatorPage extends React.PureComponent {

    static propTypes = {
        modules: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.string,
            name: PropTypes.string,
            className: PropTypes.string,
        })),
        models: PropTypes.arrayOf(PropTypes.shape({
            moduleId: PropTypes.string,
            name: PropTypes.string,
            className: PropTypes.string,
            tableName: PropTypes.string,
        })),
        forms: PropTypes.arrayOf(PropTypes.shape({
            moduleId: PropTypes.string,
            name: PropTypes.string,
            className: PropTypes.string,
        })),
        enums: PropTypes.arrayOf(PropTypes.shape({
            moduleId: PropTypes.string,
            name: PropTypes.string,
            className: PropTypes.string,
        })),
    };

    render() {
        return (
            <div className={bem.block()}>
                <Form
                    formId='ClassCreator'
                    model={ClassCreatorFormMeta}
                    layout='horizontal'
                    initialValues={{
                        classType: _get(this.props, 'match.params.classType'),
                        moduleId: _get(this.props, 'match.params.moduleId'),
                        name: _get(this.props, 'match.params.name'),
                    }}
                    fields={[
                        {
                            attribute: 'moduleId',
                        },
                        {
                            attribute: 'name',
                        },
                        {
                            attribute: 'tableName',
                        },
                        {
                            attribute: 'attributes',
                            component: FieldList,
                            model: ClassCreatorAttributeFormMeta,
                            layout: 'inline',
                            items: [
                                {
                                    attribute: 'name',
                                },
                                {
                                    attribute: 'label',
                                },
                                {
                                    attribute: 'hint',
                                },
                                {
                                    attribute: 'example',
                                },
                                {
                                    attribute: 'appType',
                                },
                                {
                                    attribute: 'defaultValue',
                                },
                                {
                                    attribute: 'isRequired',
                                    label: false,
                                },
                                {
                                    attribute: 'isPublishToFrontend',
                                    label: false,
                                },
                            ],
                        },
                    ]}
                />
            </div>
        );
    }

}
