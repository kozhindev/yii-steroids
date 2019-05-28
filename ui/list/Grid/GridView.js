import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash/get';
import _keyBy from 'lodash/keyBy';
import _isString from 'lodash/isString';

import {html} from 'components';
import {getFormId} from '../listHoc';
import Form from '../../form/Form';
import Button from '../../form/Button';
import Field from '../../form/Field';
import InsideSearchFormView from './InsideSearchFormView';

const bem = html.bem('GridView');

export default class GridView extends React.Component {

    static propTypes = {
        isLoading: PropTypes.bool,
        reverse: PropTypes.bool,
        outsideSearchForm: PropTypes.node,
        paginationSize: PropTypes.node,
        pagination: PropTypes.node,
        empty: PropTypes.node,
        items: PropTypes.arrayOf(PropTypes.object),
        columns: PropTypes.arrayOf(PropTypes.shape({
            attribute: PropTypes.string,
            label: PropTypes.node,
            hint: PropTypes.node,
            headerClassName: PropTypes.string,
            sortable: PropTypes.bool,
        })),
        renderValue: PropTypes.func,
        fetch: PropTypes.func,
        sort: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(bem.block({loading: this.props.isLoading}), this.props.className)}>
                {this.props.outsideSearchForm}
                {this.props.paginationSize}
                {this.props.reverse && (
                    <div>
                        {this.props.pagination}
                        {this.renderTable()}
                    </div>
                ) ||
                (
                    <div>
                        {this.renderTable()}
                        {this.props.pagination}
                    </div>
                )}
            </div>
        );
    }

    renderTable() {
        return (
            <table className='table table-striped'>
                <thead>
                    <tr>
                        {this.props.columns.map((column, columnIndex) => (
                            <th
                                key={columnIndex}
                                className={column.headerClassName}
                            >
                                {column.label}
                                {column.sortable && column.attribute && (
                                    <span>
                                        {column.label && <span>&nbsp;</span>}
                                        {this.renderSortButton(column.attribute, 'asc')}
                                        {this.renderSortButton(column.attribute, 'desc')}
                                    </span>
                                )}
                            </th>
                        ))}
                    </tr>
                    {this.renderInsideSearchForm()}
                </thead>
                <tbody>
                    {this.props.items && this.props.items.map((item, rowIndex) => (
                        <tr key={item[this.props.primaryKey] || rowIndex}>
                            {this.props.columns.map((column, columnIndex) => (
                                <td
                                    key={columnIndex}
                                    className={column.className}
                                    data-label={_isString(column.label) ? column.label : null}
                                >
                                    {this.props.renderValue(item, column)}
                                </td>
                            ))}
                        </tr>
                    ))}
                    {this.props.empty && (
                        <tr>
                            <td colSpan={this.props.columns.length}>
                                {this.props.empty}
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        );
    }

    renderSortButton(attribute, direction) {
        const sortKey = (direction === 'desc' ? '!' : '') + attribute;
        const isActive = [].concat(this.props.list.sort || []).includes(sortKey);
        return (
            <Button
                icon={direction === 'asc' ? 'arrow_upward' : 'arrow_downward'}
                className={isActive ? 'text-success' : 'text-secondary'}
                link
                onClick={() => this.props.sort(!isActive ? sortKey : null)}
            />
        );
    }

    renderInsideSearchForm() {
        if (!this.props.searchForm || !this.props.searchForm.fields || this.props.searchForm.layout !== 'table') {
            return;
        }
        const fields = _keyBy(
            this.props.searchForm.fields
                .map(column => _isString(column) ? {attribute: column} : column),
            'attribute'
        );
        return (
            <Form
                {...this.props.searchForm}
                formId={getFormId(this.props)}
                fields={null}
                submitLabel={null}
                layout='inline'
                onSubmit={() => this.props.fetch()}
                view={InsideSearchFormView}
            >
                {this.props.columns.map((column, columnIndex) => (
                    <td
                        key={columnIndex}
                        className={column.headerClassName}
                    >
                        {column.attribute && fields[column.attribute] && (
                            <Field
                                formId={getFormId(this.props)}
                                {...fields[column.attribute]}
                            />
                        )}
                    </td>
                ))}
            </Form>
        );
    }

}
