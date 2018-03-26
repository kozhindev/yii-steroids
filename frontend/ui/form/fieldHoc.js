import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, FieldArray, formValueSelector, change} from 'redux-form';
import _get from 'lodash-es/get';
import _upperFirst from 'lodash-es/upperFirst';

import FieldLayout from './FieldLayout';

const defaultConfig = {
    attributes: [''],
    layoutProps: null,
    list: false,
};
const selectors = {};

@connect(
    (state, props) => {
        if (!props.formId) {
            return {};
        }

        // Lazy create selector
        if (!selectors[props.formId]) {
            selectors[props.formId] = formValueSelector(props.formId);
        }
        const selector = selectors[props.formId];

        // Fetch values
        const values = {};
        props._config.attributes.map(attribute => {
            values['formValue' + _upperFirst(attribute)] = selector(state, FieldHoc.getName(props, attribute));
        });
        return values;
    }
)
class FieldHoc extends React.PureComponent {

    static propTypes = {
        attribute: PropTypes.string,
    };

    static getName(props, attribute) {
        const name = attribute ? props['attribute' + _upperFirst(attribute)] : props.attribute;
        return (props.prefix || '') + name;
    }

    static ID_COUNTER = 1;

    static generateUniqueId() {
        return 'field' + FieldHoc.ID_COUNTER++;
    }

    constructor() {
        super(...arguments);

        // Check attributes is set
        if (this.props.formId) {
            this.props._config.attributes.forEach(attribute => {
                if (!this.props['attribute' + _upperFirst(attribute)]) {
                    throw new Error(`Please set attribute names for component "${this.props._wrappedComponent.name}" in form "${this.props.formId}"`);
                }
            });
        }

        if (!this.props.formId) {
            this.state = {
                value: _get(this.props, 'input.value'),
            };
            this._fieldId = FieldHoc.generateUniqueId();
        } else {
            this._fieldId = this.props.formId + '_' + FieldHoc.getName(this.props, this.props._config.attributes[0])
        }
    }

    render() {
        const WrappedComponent = this.props._wrappedComponent;

        const inputProps = {};
        if (!this.props._config.list) {
            this.props._config.attributes.forEach(attribute => {
                inputProps[`input${_upperFirst(attribute)}`] = {
                    name: FieldHoc.getName(this.props, attribute),
                    value: this._getValue(attribute),
                    onChange: value => this._setValue(attribute, value),
                };
            });
        }

        // TODO implement values in state for list (instead of redux-form FieldArray)

        return (
            <FieldLayout
                {...this.props}
                {...this.props._config.layoutProps}
            >
                {!this.props._config.list && this.props.formId && this.props._config.attributes.map(attribute => (
                    <Field
                        key={this.props.formId + attribute}
                        name={FieldHoc.getName(this.props, attribute)}
                        component='input'
                        type='hidden'
                    />
                ))}
                {this.props._config.list && (
                    <FieldArray
                        {...this.props}
                        name={FieldHoc.getName(this.props, '')}
                        component={WrappedComponent}
                        formId={this.props.formId}
                        fieldId={this._fieldId}
                    />
                ) ||
                (
                    <WrappedComponent
                        {...this.props}
                        {...inputProps}
                        formId={this.props.formId}
                        fieldId={this._fieldId}
                    />
                )}
            </FieldLayout>
        );
    }

    _getValue(attribute) {
        if (this.props.formId) {
            return _get(this.props, 'formValue' + _upperFirst(attribute));
        } else {
            return attribute
                ? _get(this.state.value, attribute)
                : this.state.value;
        }
    }

    _setValue(attribute, value) {
        if (this.props.formId) {
            this.props.dispatch(change(this.props.formId, FieldHoc.getName(this.props, attribute), value));
        } else {
            this.setState({
                value: attribute
                    ? {
                        ...this.state.value,
                        [attribute]: value,
                    }
                    : value,
            });
        }
    }
}

export default config => WrappedComponent => class FieldHocWrapper extends React.Component {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;
    static propTypes = WrappedComponent.propTypes;
    static defaultProps = WrappedComponent.defaultProps;

    static contextTypes = {
        formId: PropTypes.string,
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
        prefix: PropTypes.string,
        layout: PropTypes.string,
        layoutProps: PropTypes.object,
    };

    render() {
        return (
            <FieldHoc
                {...this.props}
                formId={this.props.formId || this.context.formId}
                model={this.props.model || this.context.model}
                prefix={this.props.prefix || this.context.prefix}
                layout={this.props.layout || this.context.layout}
                layoutProps={{
                    ...this.context.layoutProps,
                    ...this.props.layoutProps,
                }}
                _wrappedComponent={WrappedComponent}
                _config={{
                    ...defaultConfig,
                    ...config,
                }}
            />
        );
    }

};