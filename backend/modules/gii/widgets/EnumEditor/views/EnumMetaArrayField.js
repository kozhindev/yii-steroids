import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, formValueSelector, change} from 'redux-form';
import _get from 'lodash/get';

import {html} from 'components';

import './EnumMetaArrayField.scss';
const bem = html.bem('EnumMetaArrayField');

class EnumMetaArrayField extends React.Component {

    static formId = 'EnumEditor';

    static propTypes = {
        fields: PropTypes.object,
        onKeyDown: PropTypes.func,
    };

    constructor() {
        super(...arguments);

        this.state = {
            columnName: '',
            showValues: !!this.props.formValues.meta.find(item => item.name !== item.value),
        };

        this._onAddColumn = this._onAddColumn.bind(this);
    }

    render() {
        const customColumns = _get(this.props, 'formValues.meta[0].customColumns', {});
        return (
            <div className={bem(bem.block(), 'form-inline')}>
                <div className='pull-right text-muted'>
                    <small>
                        Используйте&nbsp;
                        <span className='label label-default'>Shift</span>
                        &nbsp;+&nbsp;
                        <span className='label label-default'>↑↓</span>
                        &nbsp;для перехода по полям
                    </small>
                </div>
                <h3>
                    Enum meta information
                </h3>
                <table className='table table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            {this.state.showValues && (
                                <th>Value</th>
                            )}
                            <th>Label</th>
                            <th>Css class</th>
                            {Object.keys(customColumns).map(columnName => (
                                <th key={columnName}>
                                    {columnName}
                                    &nbsp;
                                    <a
                                        href='javascript:void(0)'
                                        onClick={() => this._onRemoveColumn(columnName)}
                                        className='text-danger'
                                    >
                                        <span className='glyphicon glyphicon-remove'/>
                                    </a>
                                </th>
                            ))}
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.fields.map((attribute, index) => (
                            <tr key={index}>
                                <td>
                                    {index + 1}
                                    <Field
                                        name={`${attribute}[customColumns]`}
                                        component='input'
                                        type='hidden'
                                    />
                                </td>
                                <td>
                                    <Field
                                        name={`${attribute}[name]`}
                                        component='input'
                                        className='form-control input-sm'
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                </td>
                                {this.state.showValues && (
                                    <td>
                                        <Field
                                            name={`${attribute}[value]`}
                                            component='input'
                                            className='form-control input-sm'
                                            onKeyDown={this.props.onKeyDown}
                                        />
                                    </td>
                                )}
                                <td>
                                    <Field
                                        name={`${attribute}[label]`}
                                        component='input'
                                        className='form-control input-sm'
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                </td>
                                <td>
                                    <Field
                                        name={`${attribute}[cssClass]`}
                                        component='input'
                                        className='form-control input-sm'
                                        list={`${EnumMetaArrayField.formId}_cssClassList`}
                                        onKeyDown={this.props.onKeyDown}
                                    />
                                </td>
                                {Object.keys(customColumns).map(columnName => (
                                    <td key={columnName}>
                                        <Field
                                            name={`${attribute}[customColumns][${columnName}]`}
                                            component='input'
                                            className='form-control input-sm'
                                            onKeyDown={this.props.onKeyDown}
                                        />
                                    </td>
                                ))}
                                <td style={{textAlign: 'right'}}>
                                    <button
                                        type='button'
                                        className={'btn btn-sm btn-danger'}
                                        onClick={() => this.props.fields.remove(index)}
                                    >
                                        <span className='glyphicon glyphicon-remove'/>
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className='row'>
                    <div className='col-sm-2'>
                        <a
                            className='btn btn-sm btn-default'
                            href='javascript:void(0)'
                            onClick={() => this.props.fields.push()}
                        >
                            <span className='glyphicon glyphicon-plus'/> Добавить
                        </a>
                    </div>
                    <div className='col-sm-2'>
                        <div className='checkbox'>
                            <label>
                                <input
                                    type='checkbox'
                                    checked={this.state.showValues}
                                    onChange={e => this.setState({showValues: e.target.checked})}
                                />
                                &nbsp;
                                Show values
                            </label>
                        </div>
                    </div>
                    <div className='col-sm-5'>
                        <input
                            className={bem(bem.element('input-add-column'), 'form-control input-sm')}
                            value={this.state.columnName}
                            onChange={e => this.setState({columnName: e.target.value})}
                            placeholder='myColumn'
                        />
                        <a
                            className='btn btn-sm btn-default'
                            href='javascript:void(0)'
                            onClick={this._onAddColumn}
                        >
                            <span className='glyphicon glyphicon-plus'/> Добавить столбец
                        </a>
                    </div>
                </div>
            </div>
        );
    }

    _onAddColumn() {
        const columnName = this.state.columnName;
        if (!columnName) {
            return;
        }

        this.setState({columnName: ''});
        const meta = this.props.formValues.meta || {};
        meta.forEach((item, i) => {
            this.props.dispatch(change(EnumMetaArrayField.formId, `meta.${i}.customColumns`, {
                ..._get(this.props, `formValues.meta.${i}.customColumns`),
                [columnName]: null,
            }));
        });
    }

    _onRemoveColumn(columnName) {
        if (!confirm('Удалить колонку?')) {
            return;
        }

        const meta = this.props.formValues.meta || {};
        meta.forEach((item, i) => {
            const columns = _get(this.props, `formValues.meta.${i}.customColumns`, {});
            delete columns[columnName];

            this.props.dispatch(change(EnumMetaArrayField.formId, `meta.${i}.customColumns`, {...columns}));
        });
    }

}

const selector = formValueSelector(EnumMetaArrayField.formId);
export default connect(
    state => ({
        formValues: {
            meta: selector(state, 'meta') || [],
        }
    })
)(EnumMetaArrayField);