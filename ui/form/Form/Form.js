import React from 'react';
import {findDOMNode} from 'react-dom';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {reduxForm, getFormValues, isInvalid} from 'redux-form';
import _isEqual from 'lodash-es/isEqual';
import _isString from 'lodash-es/isString';
import _get from 'lodash-es/get';

import {ui} from 'components';
import AutoSaveHelper from './AutoSaveHelper';
import SyncAddressBarHelper from './SyncAddressBarHelper';
import {getSecurity} from '../../../reducers/fields';
import Field from '../Field';
import Button from '../Button';
import formSubmitHoc from '../formSubmitHoc';

let valuesSelector = null;
let invalidSelector = null;

export default
@connect(
    (state, props) => {
        valuesSelector = valuesSelector || getFormValues(props.formId);
        invalidSelector = invalidSelector || isInvalid(props.formId);

        return {
            form: props.formId,
            formValues: valuesSelector(state),
            security: getSecurity(state, props.formId),
            isInvalid: invalidSelector(state),
            formRegisteredFields: _get(state, `form.${props.formId}.registeredFields`),
        };
    }
)
@formSubmitHoc()
@reduxForm()
class Form extends React.PureComponent {

    static propTypes = {
        formId: PropTypes.string.isRequired,
        prefix: PropTypes.string,
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
            PropTypes.object,
        ]),
        action: PropTypes.string,
        actionMethod: PropTypes.string,
        layout: PropTypes.oneOfType([
            PropTypes.oneOf(['default', 'inline', 'horizontal']),
            PropTypes.string,
        ]),
        layoutProps: PropTypes.object,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        onSubmit: PropTypes.func,
        onAfterSubmit: PropTypes.func,
        onChange: PropTypes.func,
        onComplete: PropTypes.func,
        autoSave: PropTypes.bool,
        initialValues: PropTypes.object,
        className: PropTypes.string,
        view: PropTypes.func,
        formValues: PropTypes.object,
        isInvalid: PropTypes.bool,
        formRegisteredFields: PropTypes.object,
        fields: PropTypes.arrayOf(PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.shape({
                label: PropTypes.string,
                hint: PropTypes.string,
                required: PropTypes.bool,
                component: PropTypes.oneOfType([
                    PropTypes.string,
                    PropTypes.func,
                ]),
            })
        ])),
        submitLabel: PropTypes.string,
        syncWithAddressBar: PropTypes.bool,
        useHash: PropTypes.bool,
        security: PropTypes.shape({
            component: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.node
            ]),
        }),
        autoFocus: PropTypes.bool,
    };

    static childContextTypes = {
        formId: PropTypes.string,
        prefix: PropTypes.string,
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
            PropTypes.object,
        ]),
        layout: PropTypes.oneOfType([
            PropTypes.oneOf(['default', 'inline', 'horizontal']),
            PropTypes.string,
        ]),
        layoutProps: PropTypes.object,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
    };

    getChildContext() {
        return {
            prefix: this.props.prefix,
            formId: this.props.formId,
            model: this.props.model,
            layout: this.props.layout,
            layoutProps: {
                ...this.props.layoutProps,
            },
            size: this.props.size,
        };
    }

    componentWillMount() {
        // Restore values from query, when autoSave flag is set
        if (this.props.autoSave) {
            AutoSaveHelper.restore(this.props.formId, this.props.initialValues);
        }

        // Restore values from address bar
        if (this.props.syncWithAddressBar) {
            SyncAddressBarHelper.restore(this.props.formId, this.props.initialValues);
        }
    }

    componentDidMount() {
        if (this.props.autoFocus) {
            const inputEl = findDOMNode(this).querySelector('input:not([type=hidden])');
            setTimeout(() => {
                if (inputEl && inputEl.focus) {
                    inputEl.focus();
                }
            }, 10);
        }
    }

    componentWillReceiveProps(nextProps) {
        // Check update values for trigger event
        if ((this.props.onChange || this.props.autoSave || this.props.syncWithAddressBar) && !_isEqual(this.props.formValues, nextProps.formValues)) {
            if (this.props.onChange) {
                this.props.onChange(nextProps.formValues);
            }
            if (this.props.autoSave && nextProps.formValues) {
                AutoSaveHelper.save(this.props.formId, nextProps.formValues);
            }
            if (this.props.syncWithAddressBar) {
                SyncAddressBarHelper.save(nextProps.formValues, nextProps.useHash);
            }
        }
    }

    render() {
        const FormView = this.props.view || ui.getView('form.FormView');
        return (
            <FormView
                {...this.props}
                onSubmit={this.props.handleSubmit(this.props.onSubmit)}
            >
                {this.props.children}
                {this.props.fields && this.props.fields.map((field, index) => (
                    <Field
                        key={index}
                        {...(_isString(field) ? {attribute: field} : field)}
                    />
                ))}
                {this.props.security && (
                    <Field {...this.props.security}/>
                )}
                {this.props.submitLabel && (
                    <Button
                        type='submit'
                        label={this.props.submitLabel}
                    />
                )}
            </FormView>
        );
    }

}
