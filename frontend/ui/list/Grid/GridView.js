import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('GridView');

export default class GridView extends React.Component {

    static propTypes = {
        isLoading: PropTypes.bool,
        reverse: PropTypes.bool,
        searchForm: PropTypes.node,
        paginationSize: PropTypes.node,
        pagination: PropTypes.node,
        empty: PropTypes.node,
        items: PropTypes.arrayOf(PropTypes.object),
        columns: PropTypes.arrayOf(PropTypes.shape({
            attribute: PropTypes.string,
            label: PropTypes.node,
            hint: PropTypes.node,
            headerClassName: PropTypes.string,
        })),
        renderValue: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                {this.props.searchForm}
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
        // TODO Hint
        // TODO Sortable
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
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {this.props.items && this.props.items.map((item, rowIndex) => (
                        <tr key={item[this.props.primaryKey] || rowIndex}>
                            {this.props.columns.map((column, columnIndex) => (
                                <td
                                    key={columnIndex}
                                    className={column.className}
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

}