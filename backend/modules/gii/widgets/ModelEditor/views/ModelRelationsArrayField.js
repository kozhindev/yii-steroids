import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, formValueSelector} from 'redux-form';

import {html} from 'components';

import './ModelRelationsArrayField.scss';
const bem = html.bem('ModelRelationsArrayField');

class ModelRelationsArrayField extends React.Component {

    static formId = 'ModelEditor';

    static propTypes = {
        fields: PropTypes.object,
        dbTypes: PropTypes.arrayOf(PropTypes.string),
        formValues: PropTypes.object,
        models: PropTypes.array,
        onKeyDown: PropTypes.func,
    };

    constructor() {
        super(...arguments);

        this.state = {
            relationFocuses: {},
            selfFocuses: {},
        };
    }

    render() {
        return (
            <div className={bem(bem.block(), 'form-inline')}>
                <h3>
                    Relations
                </h3>
                <datalist id={`${ModelRelationsArrayField.formId}_allModelNameList`}>
                    {this.props.models.map(model => (
                        <option key={model.className} value={model.className} />
                    ))}
                </datalist>
                <table className='table table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Relation key</th>
                            <th>Self key</th>
                            <th />
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.fields.map((attribute, index) => (
                            <tr key={index}>
                                <td>
                                    {index+1}
                                </td>
                                <td>
                                    <Field
                                        name={`${attribute}[type]`}
                                        component='select'
                                        className='form-control input-sm'
                                        onKeyDown={this.props.onKeyDown}
                                    >
                                        <option value='hasOne'>hasOne</option>
                                        <option value='hasMany'>hasMany</option>
                                        <option value='manyMany'>manyMany</option>
                                    </Field>
                                </td>
                                <td>
                                    <Field
                                        name={`${attribute}[name]`}
                                        component='input'
                                        className='form-control input-sm'
                                        placeholder='Name'
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                </td>
                                <td className={this._isFocused(index, true) ? 'table-success' : ''}>
                                    <datalist id={`${ModelRelationsArrayField.formId}_${index}_relationAttributesList`}>
                                        {this._getModelMeta(index).map(item => (
                                            <option key={item.name} value={item.name} />
                                        ))}
                                    </datalist>
                                    <Field
                                        name={`${attribute}[relationModelClassName]`}
                                        component='input'
                                        className='form-control input-sm'
                                        list={`${ModelRelationsArrayField.formId}_allModelNameList`}
                                        placeholder='Model class'
                                        style={{width: '47%'}}
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                    &nbsp;.&nbsp;
                                    <Field
                                        name={`${attribute}[relationKey]`}
                                        component='input'
                                        className='form-control input-sm'
                                        list={`${ModelRelationsArrayField.formId}_${index}_relationAttributesList`}
                                        placeholder='Key'
                                        style={{width: '47%'}}
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                </td>
                                <td className={this._isFocused(index, false) ? 'table-success' : ''}>
                                    <input
                                        className='form-control input-sm'
                                        defaultValue={this.props.formValues.modelName}
                                        disabled={true}
                                        style={{width: '47%'}}
                                    />
                                    &nbsp;.&nbsp;
                                    <Field
                                        name={`${attribute}[selfKey]`}
                                        component='input'
                                        className='form-control input-sm'
                                        placeholder='Key'
                                        list={`${ModelRelationsArrayField.formId}_selfAttributesList`}
                                        style={{width: '47%'}}
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                </td>
                                <td>
                                    {this._getType(index) === 'manyMany' && (
                                        <span>
                                            <Field
                                                name={`${attribute}[viaTable]`}
                                                component='input'
                                                list={`${ModelRelationsArrayField.formId}_tableNameList`}
                                                className='form-control input-sm'
                                                placeholder='Table name'
                                                style={{width: '35%'}}
                                                onKeyDown={this.props.onKeyDown}
                                            />
                                            &nbsp;
                                            <span className={this._isFocused(index, true) ? 'has-success' : ''}>
                                                <Field
                                                    name={`${attribute}[viaRelationKey]`}
                                                    component='input'
                                                    className={bem(
                                                        'form-control input-sm',
                                                        this._isFocused(index, true) && 'table-success'
                                                    )}
                                                    placeholder='Key'
                                                    onFocus={() => this._setFocus(index, true, true)}
                                                    onBlur={() => this._setFocus(index, true, false)}
                                                    style={{width: '30%'}}
                                                    onKeyDown={this.props.onKeyDown}
                                                />
                                            </span>
                                            &nbsp;
                                            <span className={this._isFocused(index, false) ? 'has-success' : ''}>
                                                <Field
                                                    name={`${attribute}[viaSelfKey]`}
                                                    component='input'
                                                    className='form-control input-sm'
                                                    placeholder='Key'
                                                    onFocus={() => this._setFocus(index, false, true)}
                                                    onBlur={() => this._setFocus(index, false, false)}
                                                    style={{width: '30%'}}
                                                    onKeyDown={this.props.onKeyDown}
                                                />
                                            </span>
                                        </span>
                                    )}
                                </td>
                                <td>
                                    <button
                                        type='button'
                                        className={'btn btn-sm btn-danger'}
                                        onClick={() => this.props.fields.remove(index)}
                                    >
                                        <span className='glyphicon glyphicon-remove' />
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div>
                    <a
                        className='btn btn-sm btn-default'
                        href='javascript:void(0)'
                        onClick={() => this.props.fields.push()}
                    >
                        <span className='glyphicon glyphicon-plus' /> Добавить
                    </a>
                </div>
            </div>
        );
    }

    _isFocused(index, isRelation) {
        const key = isRelation ? 'relationFocuses' : 'selfFocuses';
        return this.state[key][index] === true;
    }

    _setFocus(index, isRelation, isFocus) {
        const key = isRelation ? 'relationFocuses' : 'selfFocuses';
        this.setState({
            [key]: {
                ...this.state[key],
                [index]: isFocus,
            },
        });
    }

    _getType(index) {
        return this.props.formValues.relations[index] && this.props.formValues.relations[index].type || null;
    }

    _getModelMeta(index) {
        const relationModelClassName = this.props.formValues.relations[index] && this.props.formValues.relations[index].relationModelClassName || null;
        const model = this.props.models.find(model => model.className === relationModelClassName);
        return model ? model.metaClass.meta : [];
    }

}

const selector = formValueSelector(ModelRelationsArrayField.formId);
export default connect(
    state => ({
        formValues: {
            modelName: selector(state, 'modelName'),
            meta: selector(state, 'meta'),
            relations: selector(state, 'relations'),
        }
    })
)(ModelRelationsArrayField);