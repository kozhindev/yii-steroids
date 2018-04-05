import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import ModelAttributeRow from './ModelAttributeRow';

import './ModelAttributesArrayField.scss';
const bem = html.bem('ModelAttributesArrayField');

export default class ModelAttributesArrayField extends React.Component {

    static formId = 'CrudForm';

    static propTypes = {
        fields: PropTypes.object,
        keys: PropTypes.array,
    };

    render() {
        const labels = {
            showInForm: 'Show in form',
            showInFilter: 'Show in filter',
            showInTable: 'Show in table',
            showInView: 'Show in view',
        };

        return (
            <div className={bem(bem.block(), 'form-inline')}>
                <table className='table table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            {this.props.keys.map(key => (
                                <th
                                    key={key}
                                    className={bem.element('th-small')}
                                >
                                    {labels[key]}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.fields.map((attribute, index) => (
                            <ModelAttributeRow
                                key={index}
                                attribute={attribute}
                                index={index}
                                keys={this.props.keys}
                            >
                            </ModelAttributeRow>
                        ))}
                    </tbody>
                </table>
            </div>
        );
    }

}
