import React from 'react';
import PropTypes from 'prop-types';

import {ui} from 'components';
import Field from '../Field';
import fieldHoc from '../fieldHoc';

import './FieldListView.scss';

@fieldHoc({
    componentId: 'form.FieldList',
    list: true,
})
export default class FieldList extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        attribute: PropTypes.string,
        items: PropTypes.arrayOf(PropTypes.shape({
            label: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.bool,
                PropTypes.element,
            ]),
            hint: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.bool,
                PropTypes.element,
            ]),
            attribute: PropTypes.string,
            prefix: PropTypes.string,
            visible: PropTypes.bool,
            model: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
            ]),
            component: PropTypes.any,
            required: PropTypes.bool,
            size: PropTypes.oneOf(['sm', 'md', 'lg']),
            placeholder: PropTypes.string,
            disabled: PropTypes.bool,
            onChange: PropTypes.func,
            className: PropTypes.string,
            headerClassName: PropTypes.string,
            view: PropTypes.func,
        })),
        fields: PropTypes.object,
        required: PropTypes.bool,
        disabled: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        initialRowsCount: PropTypes.number,
        showAdd: PropTypes.bool,
        showRemove: PropTypes.bool,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
        viewProps: PropTypes.object,
        itemView: PropTypes.func,
        itemViewProps: PropTypes.object,
    };

    static defaultProps = {
        disabled: false,
        required: false,
        showAdd: true,
        showRemove: true,
        className: '',
        initialRowsCount: 1,
    };

    static contextTypes = {
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
    };

    static childContextTypes = {
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
        prefix: PropTypes.string,
        layout: PropTypes.string,
        layoutProps: PropTypes.object,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
    };

    getChildContext() {
        return {
            model: this.props.model || this.context.model,
            prefix: (this.context.prefix || '') + (this.props.prefix || ''),
            layout: this.props.layout || this.context.layout,
            layoutProps: {
                ...this.context.layoutProps,
                ...this.props.layoutProps,
            },
            size: this.props.size || this.context.size,
        };
    }

    constructor() {
        super(...arguments);

        this._onAdd = this._onAdd.bind(this);
        this._onRemove = this._onRemove.bind(this);
        this._renderField = this._renderField.bind(this);
    }

    componentWillMount() {
        if (this.props.fields.length === 0) {
            for (let i = 0; i < this.props.initialRowsCount; i++) {
                this._onAdd();
            }
        }
    }

    render() {
        const FieldListView = this.props.view || ui.getView('form.FieldListView');
        const FieldListItemView = this.props.itemView || ui.getView('form.FieldListItemView');
        const items = (this.props.items || [])
            .filter(field => field.visible !== false)
            .map(field => ({
                ...Field.getFieldPropsFromModel(this.props.model || this.context.model, field.attribute),
                ...field,
                disabled: field.disabled || this.props.disabled,
                size: field.size || this.props.size,
            }));

        return (
            <FieldListView
                {...this.props}
                {...this.props.view}
                items={items}
                renderField={this._renderField}
                onAdd={this._onAdd}
            >
                {this.props.fields.map((prefix, rowIndex) => (
                    <FieldListItemView
                        {...this.props}
                        {...this.props.itemViewProps}
                        items={items}
                        renderField={this._renderField}
                        onRemove={this._onRemove}
                        key={rowIndex}
                        prefix={prefix}
                        rowIndex={rowIndex}
                    />
                ))}
            </FieldListView>
        );
    }

    _renderField(field, prefix) {
        return (
            <Field
                layout='inline'
                {...field}
                prefix={prefix}
            />
        );
    }

    _onAdd() {
        this.props.fields.push();
    }

    _onRemove(rowIndex) {
        this.props.fields.remove(rowIndex);
    }

}
