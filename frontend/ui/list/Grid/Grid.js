import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import _isString from 'lodash-es/isString';

import {ui} from 'components';
import listHoc from '../listHoc';
import ActionColumn from '../ActionColumn';
import Format from '../../format/Format';

@listHoc()
export default class Grid extends React.PureComponent {

    static propTypes = {
        primaryKey: PropTypes.string,
        view: PropTypes.func,
        columns: PropTypes.arrayOf(PropTypes.shape({
            attribute: PropTypes.string,
            format: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.shape({
                    component: PropTypes.oneOfType([
                        PropTypes.string,
                        PropTypes.func,
                    ]),
                }),
            ]),
            label: PropTypes.node,
            hint: PropTypes.node,
            headerClassName: PropTypes.string,
            visible: PropTypes.bool,
            valueView: PropTypes.func,
            valueProps: PropTypes.object,
        })).isRequired,
        actions: PropTypes.arrayOf(PropTypes.object),
    };

    constructor() {
        super(...arguments);

        this.renderValue = this.renderValue.bind(this);
    }

    render() {
        const columns = this.props.columns.filter(column => column.visible !== false);
        if (this.props.actions) {
            columns.push({
                valueView: ActionColumn,
                valueProps: {
                    actions: this.props.actions,
                },
            });
        }

        const GridView = this.props.view || ui.getView('list.GridView');
        return (
            <GridView
                {...this.props}
                renderValue={this.renderValue}
                columns={columns}
            />
        );
    }

    renderValue(item, column) {
        // Custom component
        if (column.valueView) {
            const ValueView = column.valueView;
            return (
                <ValueView
                    {...column}
                    {...column.valueProps}
                    primaryKey={this.props.primaryKey}
                    item={item}
                />
            );
        }

        // Formatter
        if (column.format) {
            const format = _isString(column.format) ? {component: column.format} : column.format;
            return (
                <Format
                    attribute={column.attribute}
                    item={item}
                    {...format}
                />
            );
        }

        // Single value
        return _get(item, column.attribute);
    }

}