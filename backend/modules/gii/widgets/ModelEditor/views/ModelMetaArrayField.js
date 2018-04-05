import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import ModelMetaRow from './ModelMetaRow';

import './ModelMetaArrayField.scss';
const bem = html.bem('ModelMetaArrayField');

class ModelMetaArrayField extends React.Component {

    static formId = 'ModelEditor';

    static propTypes = {
        fields: PropTypes.object,
        appTypes: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string
        })),
        isAR: PropTypes.bool,
        onKeyDown: PropTypes.func,
    };

    render() {
        const isAR = this.props.isAR;
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
                    {isAR ? 'Attributes meta information' : 'Form fields'}
                </h3>
                <table className='table table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Label</th>
                            <th>Hint</th>
                            <th className={bem.element('th-app-types')}>
                                Type
                            </th>
                            {isAR && (
                                <th className={bem(bem.element('th-small'), bem.element('th-default-value'))}>
                                    Default value
                                </th>
                            )}
                            <th className={bem.element('th-small')}>
                                Required
                            </th>
                            {isAR && (
                                <th className={bem(bem.element('th-small'), bem.element('th-publish'))}>
                                    Publish to frontend
                                </th>
                            )}
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.fields.map((attribute, index) => (
                            <ModelMetaRow
                                key={index}
                                attribute={attribute}
                                index={index}
                                appTypes={this.props.appTypes}
                                onKeyDown={this.props.onKeyDown}
                                onRemove={() => this.props.fields.remove(index)}
                                isAR={isAR}
                            >
                            </ModelMetaRow>
                        ))}
                    </tbody>
                </table>
                <div>
                    <a
                        className='btn btn-sm btn-default'
                        href='javascript:void(0)'
                        onClick={() => this.props.fields.push()}
                    >
                        <span className='glyphicon glyphicon-plus'/> Добавить
                    </a>
                </div>
            </div>
        );
    }

}

export default ModelMetaArrayField;