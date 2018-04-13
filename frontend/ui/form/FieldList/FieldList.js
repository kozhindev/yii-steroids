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
        itemView: PropTypes.func,
    };

    static defaultProps = {
        size: 'md',
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
        return (
            <FieldListView
                {...this.props}
                rows={this.props.fields.map(prefix => prefix)}
                items={(this.props.items || [])
                    .map(field => ({
                        ...Field.getFieldPropsFromModel(this.props.model || this.context.model, field.attribute),
                        ...field,
                        disabled: field.disabled || this.props.disabled,
                        size: field.size || this.props.size,
                    }))
                }
                renderField={this._renderField}
                onAdd={this._onAdd}
                onRemove={this._onRemove}
            />
        );
    }

    _renderField(field, prefix) {
        return (
            <Field
                {...field}
                layout='inline'
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
