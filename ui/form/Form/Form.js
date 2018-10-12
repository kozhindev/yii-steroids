import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {reduxForm, SubmissionError, getFormValues, isInvalid} from 'redux-form';
import _isEqual from 'lodash-es/isEqual';
import _get from 'lodash-es/get';
import _set from 'lodash-es/set';
import _isUndefined from 'lodash-es/isUndefined';

import {http, ui} from 'components';
import {addSecurity} from '../../../actions/fields';
import AutoSaveHelper from './AutoSaveHelper';
import SyncAddressBarHelper from './SyncAddressBarHelper';
import {getSecurity} from '../../../reducers/fields';
import Field from '../Field';
import Button from '../Button';

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
@reduxForm()
class Form extends React.PureComponent {

    static propTypes = {
        formId: PropTypes.string.isRequired,
        prefix: PropTypes.string,
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
        action: PropTypes.string,
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
        fields: PropTypes.arrayOf(PropTypes.shape({
            label: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.bool,
            ]),
            hint: PropTypes.string,
            required: PropTypes.bool,
            component: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
            ]),
        })),
        submitLabel: PropTypes.string,
        syncWithAddressBar: PropTypes.bool,
        security: PropTypes.shape({
            component: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.node
            ]),
        }),
    };

    static childContextTypes = {
        formId: PropTypes.string,
        prefix: PropTypes.string,
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
        layout: PropTypes.oneOfType([
            PropTypes.oneOf(['default', 'inline', 'horizontal']),
            PropTypes.string,
        ]),
        layoutProps: PropTypes.object,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
    };

    constructor() {
        super(...arguments);

        this._onSubmit = this._onSubmit.bind(this);
    }

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

    componentWillReceiveProps(nextProps) {
        // Check update values for trigger event
        if ((this.props.onChange || this.props.autoSave || this.props.syncWithAddressBar) && !_isEqual(this.props.formValues, nextProps.formValues)) {
            if (this.props.onChange) {
                this.props.onChange(nextProps.formValues);
            }
            if (this.props.autoSave) {
                AutoSaveHelper.save(this.props.formId, nextProps.formValues);
            }
            if (this.props.syncWithAddressBar) {
                SyncAddressBarHelper.save(nextProps.formValues);
            }
        }
    }

    render() {
        const FormView = this.props.view || ui.getView('form.FormView');
        return (
            <FormView
                {...this.props}
                onSubmit={this.props.handleSubmit(this._onSubmit)}
            >
                {this.props.children}
                {this.props.fields && this.props.fields.map((field, index) => (
                    <Field
                        key={index}
                        {...field}
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

    _onSubmit(values) {
        // Append non touched fields to values object
        Object.keys(this.props.formRegisteredFields || {}).forEach(key => {
            const name = this.props.formRegisteredFields[key].name;
            if (_isUndefined(_get(values, name))) {
                _set(values, name, null);
            }
        });

        if (this.props.onSubmit) {
            return this.props.onSubmit(values);
        }

        return http.post(this.props.action || location.pathname, values)
            .then(response => {
                if (response.security) {
                    this.props.dispatch(addSecurity(this.props.formId, {
                        ...response.security,
                        onSuccess: data => this._onSubmit({...values, ...data}),
                    }));
                }
                if (response.errors) {
                    throw new SubmissionError(response.errors);
                }
                if (!response.security) {
                    if (this.props.autoSave) {
                        AutoSaveHelper.remove(this.props.formId);
                    }
                    if (this.props.onComplete) {
                        this.props.onComplete(values, response);
                    }
                }
            });
    }

}
