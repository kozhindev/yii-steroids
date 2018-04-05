import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, formValueSelector, arrayPush} from 'redux-form';
import _isEqual from 'lodash/isEqual';

import {html} from 'components';

import './ModelMetaArrayField.scss';

const bem = html.bem('ModelMetaArrayField');

class ModelMetaRow extends React.Component {

    static formId = 'ModelEditor';

    static propTypes = {
        fields: PropTypes.object,
        appTypes: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string
        })),
        formValues: PropTypes.object,
        onKeyDown: PropTypes.func,
        onRemove: PropTypes.func,
        isAR: PropTypes.bool,
    };

    shouldComponentUpdate(nextProps) {
        return !!['name', 'appType', 'isShowButtonAddRelation'].find(name => {
            return !_isEqual(this.props[name], nextProps[name]);
        });
    }

    render() {
        const {attribute, index, isAR} = this.props;

        return (
            <tr key={index}>
                <td>
                    {index + 1}
                </td>
                <td>
                    <Field
                        name={`${attribute}[name]`}
                        component='input'
                        className='form-control input-sm'
                        onKeyDown={this.props.onKeyDown}
                    />
                    <Field
                        name={`${attribute}[oldName]`}
                        component='input'
                        type='hidden'
                    />
                </td>
                <td>
                    <Field
                        name={`${attribute}[label]`}
                        component='input'
                        className='form-control input-sm'
                        onKeyDown={this.props.onKeyDown}
                    />
                </td>
                <td className={bem.element('field-hint')}>
                    <Field
                        name={`${attribute}[hint]`}
                        component='input'
                        className='form-control input-sm'
                        onKeyDown={this.props.onKeyDown}
                    />
                </td>
                <td className={bem(bem.element('td-app-types'), 'form-inline')}>
                    <div className='form-group'>
                        <Field
                            name={`${attribute}[appType]`}
                            component='select'
                            className='form-control input-sm'
                            onKeyDown={this.props.onKeyDown}
                        >
                            <option value=''/>
                            {this.props.appTypes.map(appType => (
                                <option key={appType.name} value={appType.name}>{appType.title}</option>
                            ))}
                        </Field>
                    </div>
                    {this._renderAppTypeFields()}
                </td>
                {isAR && (
                    <td className={bem.element('td-default-value')}>
                        <Field
                            name={`${attribute}[defaultValue]`}
                            component='input'
                            className='form-control input-sm'
                            onKeyDown={this.props.onKeyDown}
                        />
                    </td>
                )}
                <td>
                    <div className={bem(bem.element('td-checkbox'), 'checkbox')}>
                        <label>
                            {this.props.appType === 'primaryKey' && (
                                <input
                                    type='checkbox'
                                    checked={true}
                                    disabled={true}
                                />
                            ) ||
                            (
                                <Field
                                    name={`${attribute}[required]`}
                                    component='input'
                                    type='checkbox'
                                    onKeyDown={this.props.onKeyDown}
                                />
                            )}
                        </label>
                    </div>
                </td>
                {isAR && (
                    <td>
                        <div className={bem(bem.element('td-checkbox'), 'checkbox')}>
                            <label>
                                <Field
                                    name={`${attribute}[publishToFrontend]`}
                                    component='input'
                                    type='checkbox'
                                    onKeyDown={this.props.onKeyDown}
                                />
                            </label>
                        </div>
                    </td>
                )}
                <td style={{width: 90, textAlign: 'right'}}>
                    {this.props.isShowButtonAddRelation && (
                        <button
                            type='button'
                            className={'btn btn-sm btn-primary'}
                            onClick={() => this._addRelation()}
                            title='Add hasOne relation'
                        >
                            <span className='glyphicon glyphicon-link'/>
                        </button>
                    )}
                    &nbsp;
                    <button
                        type='button'
                        className={'btn btn-sm btn-danger'}
                        onClick={this.props.onRemove}
                    >
                        <span className='glyphicon glyphicon-remove'/>
                    </button>
                </td>
            </tr>
        );
    }

    _renderAppTypeFields() {
        const attribute = this.props.attribute;
        const appType = this.props.appTypes.find(t => t.name === this.props.appType);
        if (!appType) {
            return;
        }

        return Object.keys(appType.fieldProps).map(key => {
            const id = `${attribute}_${appType.name}_${key}_input`;
            const {label, list, options, ...props} = appType.fieldProps[key];

            const inputProps = {
                component: 'input',
                ...props,
                id,
                name: `${attribute}[${key}]`,
                list: list ? `${ModelMetaRow.formId}_${key}` : undefined,
                onKeyDown: this.props.onKeyDown
            };

            switch (props.component) {
                case 'select':
                    return (
                        <div key={key} className='form-group'>
                            <Field
                                {...inputProps}
                                className='form-control input-sm'
                                placeholder={label || appType.name}
                            >
                                {Object.keys(options || {}).map(key => (
                                    <option key={key} value={key}>{options[key]}</option>
                                ))}
                            </Field>
                        </div>
                    );

                case 'input':
                    switch (props.type) {
                        case 'checkbox':
                            return (
                                <div key={key} className='checkbox'>
                                    <label>
                                        <Field {...inputProps} />
                                        &nbsp;
                                        {label || appType.name}
                                    </label>
                                </div>
                            );
                    }
                    break;
            }

            return (
                <div key={key} className='form-group'>
                    <Field
                        {...inputProps}
                        placeholder={label || appType.name}
                        className='form-control input-sm'
                    />
                </div>
            );
        });
    }

    _addRelation() {
        this.props.dispatch(arrayPush(ModelMetaRow.formId, 'relations', {
            type: 'hasOne',
            name: this.props.name.replace('Id', ''),
            relationModelClassName: '',
            relationKey: 'id',
            selfKey: this.props.name,
        }));
    }

}

const selector = formValueSelector(ModelMetaRow.formId);
export default connect(
    (state, props) => {
        const meta = selector(state, 'meta')[props.index] || {};
        const relations = selector(state, 'relations') || [];
        const name = meta.name || '';

        return {
            name,
            appType: meta.appType || null,
            isShowButtonAddRelation: props.isAR && name.indexOf('Id') !== -1 && !relations.find(relation => {
                return relation && relation.selfKey === name;
            })
        };
    }
)(ModelMetaRow);