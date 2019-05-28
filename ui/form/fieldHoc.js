import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, FieldArray, formValueSelector, getFormSubmitErrors, change} from 'redux-form';
import _get from 'lodash-es/get';
import _upperFirst from 'lodash-es/upperFirst';
import _isEqual from 'lodash-es/isEqual';

import {ui} from 'components';
import FieldLayout from './FieldLayout';
import {getFieldProps} from '../../reducers/fields';

const defaultConfig = {
    componentId: '',
    attributes: [''],
    layoutProps: null,
    list: false,
};
const valueSelectors = {};
const errorSelectors = {};

@connect(
    (state, props) => {
        if (!props.formId) {
            return {};
        }

        // Lazy create value selector
        if (!valueSelectors[props.formId]) {
            valueSelectors[props.formId] = formValueSelector(props.formId);
        }
        const valueSelector = valueSelectors[props.formId];

        // Fetch values
        const values = {};
        props._config.attributes.map(attribute => {
            values[attribute] = valueSelector(state, FieldHoc.getName(props, attribute));
        });

        // Lazy create error selector
        if (!errorSelectors[props.formId]) {
            errorSelectors[props.formId] = getFormSubmitErrors(props.formId);
        }
        const errorSelector = errorSelectors[props.formId];

        return {
            values,
            formErrors: errorSelector(state),
            fieldProps: getFieldProps(state, FieldHoc.getFieldId(props)),
        };
    }
)
class FieldHoc extends React.Component {

    static propTypes = {
        attribute: PropTypes.string,
        fieldProps: PropTypes.object,
        formErrors: PropTypes.object,
    };

    static getAttribute(props, attribute) {
        return attribute ? props['attribute' + _upperFirst(attribute)] : props.attribute;
    }

    static getName(props, attribute) {
        return [props.prefix, FieldHoc.getAttribute(props, attribute)].filter(Boolean).join('.');
    }

    static getFieldId(props) {
        return props.formId + '_' + FieldHoc.getName(props, props._config.attributes[0]);
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
                    throw new Error(`Please set attribute name "${attribute}" for component "${this.props._wrappedComponent.name}" in form "${this.props.formId}"`);
                }
            });
        }

        if (!this.props.formId) {
            const state = {};
            this.props._config.attributes.forEach(attribute => {
                state['value' + attribute] = _get(this.props, ['input', 'value', attribute].filter(Boolean));
            });
            this.state = state;
            this._fieldId = FieldHoc.generateUniqueId();
        } else {
            this._fieldId = FieldHoc.getFieldId(this.props);
        }
    }

    shouldComponentUpdate(nextProps, nextState) {
        return !_isEqual(this.props.values, nextProps.values)
            || !_isEqual(this.props.label, nextProps.label)
            || !_isEqual(this.props.formErrors, nextProps.formErrors)
            || !_isEqual(this.props.fieldProps, nextProps.fieldProps)
            || !_isEqual(this.state, nextState);
    }

    render() {
        let {_wrappedComponent, _config, ...props} = this.props;
        const WrappedComponent = _wrappedComponent;

        const inputProps = {};
        if (!_config.list) {
            _config.attributes.forEach(attribute => {
                inputProps[`input${_upperFirst(attribute)}`] = {
                    name: FieldHoc.getName(props, attribute),
                    value: this._getValue(attribute),
                    onChange: value => this._setValue(attribute, value),
                };
            });
        }

        // Append custom field props from redux state and UiComponent
        props = {
            ...ui.getFieldProps(_config.componentId),
            ...props.fieldProps,
            ...props,
        };

        // Get errors
        let errors = this.props.errors;
        Object.keys(inputProps).map(key => {
            const name = inputProps[key].name;
            const error = _get(this.props.formErrors, name);
            if (error) {
                errors = (errors || []).concat(error);
            }
        });
        const isInvalid = errors && errors.length > 0;

        // TODO implement values in state for list (instead of redux-form FieldArray)

        return (
            <FieldLayout
                {...props}
                {..._config.layoutProps}
                errors={isInvalid ? errors : null}
                isInvalid={isInvalid}
            >
                {!_config.list && props.formId && _config.attributes.map(attribute => (
                    <Field
                        key={props.formId + attribute}
                        name={FieldHoc.getName(props, attribute)}
                        component='input'
                        type='hidden'
                    />
                ))}
                {_config.list && (
                    <FieldArray
                        {...props}
                        name={FieldHoc.getName(props, '')}
                        component={WrappedComponent}
                        formId={props.formId}
                        fieldId={this._fieldId}
                    />
                ) ||
                (
                    <WrappedComponent
                        {...props}
                        {...inputProps}
                        isInvalid={isInvalid}
                        formId={props.formId}
                        fieldId={this._fieldId}
                    />
                )}
            </FieldLayout>
        );
    }

    _getValue(attribute) {
        if (this.props.formId) {
            return _get(this.props.values, attribute);
        } else {
            return this.state['value' + attribute];
        }
    }

    _setValue(attribute, value) {
        if (this.props.formId) {
            this.props.dispatch(change(this.props.formId, FieldHoc.getName(this.props, attribute), value));
        } else {
            this.setState({
                ['value' + attribute]: value,
            });
        }
    }
}

export default config => WrappedComponent => class FieldHocWrapper extends React.PureComponent {

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
            PropTypes.object,
        ]),
        prefix: PropTypes.string,
        layout: PropTypes.string,
        layoutProps: PropTypes.object,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
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
                size={this.props.size || this.context.size || 'md'}
                _wrappedComponent={WrappedComponent}
                _config={{
                    ...defaultConfig,
                    componentId: 'form.' + (WrappedComponent.displayName || WrappedComponent.name),
                    ...config,
                }}
            />
        );
    }

};
