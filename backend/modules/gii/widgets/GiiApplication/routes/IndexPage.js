import React from 'react';
import PropTypes from 'prop-types';
import {Link} from 'yii-steroids/frontend/ui/nav';

import {html} from 'components';
import ClassTypeMeta from '../../../enums/meta/ClassTypeMeta';

import './IndexPage.scss';

const bem = html.bem('IndexPage');

export default class IndexPage extends React.PureComponent {

    static propTypes = {
        classes: PropTypes.shape({
            module: PropTypes.arrayOf(PropTypes.shape({
                id: PropTypes.string,
                name: PropTypes.string,
                className: PropTypes.string,
            })),
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
    };

    render() {
        return (
            <div className={bem.block()}>
                <table className='table table-sm'>
                    <thead>
                        <tr>
                            <th/>
                            <th>
                                Models
                            </th>
                            <th>
                                Form models
                            </th>
                            <th>
                                Enums
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.classes && this.props.classes.module.map(module => (
                            <tr key={module.id}>
                                <th className='pl-2'>
                                    {module.id}
                                </th>
                                {ClassTypeMeta.getKeys().map(key => (
                                    <td key={key}>
                                        <div className='list-group'>
                                            {this.props.classes[key].filter(item => item.moduleId === module.id).map(item => (
                                                <Link
                                                    key={item.name}
                                                    className={bem(bem.element('link'), 'list-group-item list-group-item-action')}
                                                    to={`/gii/${key}/${module.id}/${item.name}`}
                                                >
                                                    {item.name}
                                                </Link>
                                            ))}
                                        </div>
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        );
    }

}
