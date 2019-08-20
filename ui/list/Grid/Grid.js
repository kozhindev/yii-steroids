import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import _upperFirst from 'lodash-es/upperFirst';
import _isString from 'lodash-es/isString';
import _isObject from 'lodash-es/isObject';
import _isFunction from 'lodash-es/isFunction';

import {ui} from 'components';
import listHoc from '../listHoc';
import ActionColumn from '../ActionColumn';
import Format from '../../format/Format';

export default
@listHoc()
class Grid extends React.PureComponent {

    static propTypes = {
        primaryKey: PropTypes.string,
        view: PropTypes.func,
        columns: PropTypes.arrayOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.shape({
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
            }),
        ])).isRequired,
        actions: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.object),
            PropTypes.func,
        ]),
    };

    constructor() {
        super(...arguments);

        this.renderValue = this.renderValue.bind(this);
    }

    render() {
        if ((this.props.scope || []).includes('model') && !this.props.list.isFetched) {
            return null;
        }

        const columns = this.props.columns
            .map(column => _isString(column) ? {attribute: column} : column)
            .filter(column => column.visible !== false);
        if (this.props.actions) {
            columns.push({
                valueView: ActionColumn,
                valueProps: {
                    actions: this.props.actions,
                },
            });
        }

        if (this.props.itemsIndexing) {
            columns.unshift({
                label: __('№'),
                valueView: props => props.item.index,
            });
        }

        const GridView = this.props.view || ui.getView('list.GridView');
        return (
            <GridView
                {...this.props}
                renderValue={this.renderValue}
                columns={columns.map(column => ({
                    ...column,
                    label: this.renderLabel(column),
                }))}
            />
        );
    }

    renderLabel(column) {
        if (column.label || column.label === '') {
            return column.label;
        }

        const autoLabel = _upperFirst(column.attribute);
        if (_isObject(this.props.model)) {
            if (_isFunction(this.props.model.formatters)) {
                return _get(this.props.model.formatters(), [column.attribute, 'label']) || autoLabel;
            }
            if (_get(this.props, ['model', 'formatters', column.attribute])) {
                return this.props.model.formatters[column.attribute].label || autoLabel;
            }
        }
        if (_isObject(_get(this.props, 'searchForm.model'))) {
            if (_isFunction(this.props.searchForm.model.fields)) {
                return _get(this.props.searchForm.model.fields(), [column.attribute, 'label']) || autoLabel;
            }
            if (_get(this.props, ['searchForm', 'model', 'fields', column.attribute])) {
                return this.props.searchForm.model.fields[column.attribute].label || autoLabel;
            }
        }

        return autoLabel;
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

        return (
            <Format
                item={item}
                model={this.props.model}
                {...column}
            />
        );
    }

}
