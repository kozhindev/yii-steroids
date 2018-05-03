import React from 'react';
import PropTypes from 'prop-types';
import {Link} from 'yii-steroids/frontend/ui/nav';

import {html} from 'components';
import ClassTypeMeta from '../../../enums/meta/ClassTypeMeta';

import './IndexPage.scss';

const bem = html.bem('IndexPage');

export default class IndexPage extends React.PureComponent {

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
    };

    render() {
        return (
            <div className={bem.block()}>
                <table className='table table-sm'>
                    <thead>
                        <tr>
                            <th/>
                            {ClassTypeMeta.getKeys().map(key => (
                                <th key={key}>
                                    {ClassTypeMeta.getLabel(key)}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.classes && this.props.moduleIds.map(moduleId => (
                            <tr key={moduleId}>
                                <th className='pl-2'>
                                    {moduleId}
                                </th>
                                {ClassTypeMeta.getKeys().map(key => (
                                    <td key={key}>
                                        <div className='list-group'>
                                            {this.props.classes[key] && this.props.classes[key].filter(item => item.moduleId === moduleId).map(item => (
                                                <Link
                                                    key={item.name}
                                                    className={bem(bem.element('link'), 'list-group-item list-group-item-action')}
                                                    to={`/${key}/${moduleId}/${item.name}`}
                                                >
                                                    {item.name}
                                                </Link>
                                            ))}
                                        </div>
                                        <Link
                                            className={bem.element('add')}
                                            to={`/${key}/${moduleId}`}
                                        >
                                            + Add {ClassTypeMeta.getLabel(key)}
                                        </Link>
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
