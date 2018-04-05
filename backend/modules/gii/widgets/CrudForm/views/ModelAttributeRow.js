import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {formValueSelector} from 'redux-form';
import {Field} from 'redux-form';

import {html} from 'components';

const bem = html.bem('ModelAttributesArrayField');

@connect(
    (state, props) => ({
        name: formValueSelector('CrudForm')(state, `${props.attribute}[name]`),
    })
)
export default class ModelMetaRow extends React.Component {

    static formId = 'CrudForm';

    static propTypes = {
        attribute: PropTypes.string,
        index: PropTypes.number,
        keys: PropTypes.array,
    };

    render() {
        return (
            <tr>
                <td>
                    {this.props.index + 1}
                </td>
                <td>
                    {this.props.name}
                    <Field
                        name={`${this.props.attribute}[name]`}
                        component='input'
                        type='hidden'
                    />
                </td>
                {this.props.keys.map(key => (
                    <td key={key}>
                        <div className={bem(bem.element('td-checkbox'), 'checkbox')}>
                            <label>
                                <Field
                                    name={`${this.props.attribute}[${key}]`}
                                    component='input'
                                    type='checkbox'
                                />
                            </label>
                        </div>
                    </td>
                ))}
            </tr>
        );
    }

}
