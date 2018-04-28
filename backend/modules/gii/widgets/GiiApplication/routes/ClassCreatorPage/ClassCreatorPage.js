import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import {Link} from 'yii-steroids/frontend/ui/nav';

import {html} from 'components';
import ClassTypeMeta from '../../../../enums/meta/ClassTypeMeta';
import CrudCreatorView from './views/CrudCreatorView';
import EnumCreatorView from './views/EnumCreatorView';
import ModelCreatorView from './views/ModelCreatorView';
import WidgetCreatorView from './views/WidgetCreatorView';

import './ClassCreatorPage.scss';

const bem = html.bem('ClassCreatorPage');

export default class ClassCreatorPage extends React.PureComponent {

    static propTypes = {
        moduleIds: PropTypes.arrayOf(PropTypes.string),
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
        appTypes: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string,
            title: PropTypes.string,
            additionalFields: PropTypes.arrayOf(PropTypes.shape({
                attribute: PropTypes.string,
                component: PropTypes.string,
                label: PropTypes.string,
            })),
        })),
    };

    render() {
        const values = {
            classType: _get(this.props, 'match.params.classType'),
            moduleId: _get(this.props, 'match.params.moduleId'),
            name: _get(this.props, 'match.params.name'),
        };
        const entity = _get(this.props, ['classes', values.classType], []).find(item => {
            return item.moduleId === values.moduleId && item.name === values.name;
        });
        if (!entity && values.moduleId && values.name) {
            // Wait init for for set initial values
            return null;
        }

        const viewMap = {
            [ClassTypeMeta.CRUD]: CrudCreatorView,
            [ClassTypeMeta.ENUM]: EnumCreatorView,
            [ClassTypeMeta.MODEL]: ModelCreatorView,
            [ClassTypeMeta.FORM]: ModelCreatorView,
            [ClassTypeMeta.WIDGET]: WidgetCreatorView,
        };
        const CreatorView = viewMap[values.classType];

        return (
            <div className={bem.block()}>
                <nav aria-label='breadcrumb'>
                    <ol className='breadcrumb'>
                        <li className='breadcrumb-item'>
                            <Link to='/'>
                                Сущности
                            </Link>
                        </li>
                        {entity && [
                            (
                                <li
                                    key={0}
                                    className='breadcrumb-item active'
                                >
                                    {entity.moduleId}
                                </li>
                            ),
                            (
                                <li
                                    key={1}
                                    className='breadcrumb-item active'
                                >
                                    {values.classType}
                                </li>
                            ),
                            (
                                <li
                                    key={2}
                                    className='breadcrumb-item active'
                                >
                                    {entity.name}
                                </li>
                            ),
                        ] ||
                        (
                            <li className='breadcrumb-item active'>
                                Создание новой
                            </li>
                        )}
                    </ol>
                </nav>
                <h1>
                    {entity ? 'Редактирование сущности' : 'Создание сущности'}
                </h1>
                <CreatorView
                    entity={entity}
                    initialValues={{
                        ...values,
                        ...entity,
                    }}
                    classType={values.classType}
                    appTypes={this.props.appTypes}
                />
            </div>
        );
    }

}
