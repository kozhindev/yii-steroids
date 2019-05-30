import React from 'react';
import PropTypes from 'prop-types';
import {http} from 'components';
import {SubmissionError} from 'redux-form';
import {addSecurity, removeSecurity} from '../../actions/fields';
import AutoSaveHelper from './Form/AutoSaveHelper';
import _isUndefined from 'lodash-es/isUndefined';
import _set from 'lodash-es/set';
import _get from 'lodash-es/get';

export default () => WrappedComponent => class FormSubmitHoc extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;
    static propTypes = WrappedComponent.propTypes;
    static defaultProps = {
        ...WrappedComponent.defaultProps,
        actionMethod: 'POST',
    };

    static contextTypes = {
        formId: PropTypes.string,
        formRegisteredFields: PropTypes.object,
    };

    constructor(props) {
        super(props);

        this._onSubmit = this._onSubmit.bind(this);
    }


    render() {
        return (
            <WrappedComponent
                {...this.props}
                onSubmit={this._onSubmit}
            />
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

        return http.send(this.props.actionMethod, this.props.action || location.pathname, values)
            .then(response => {
                const data = response.data || {};
                if (data.security) {
                    return new Promise(resolve => {
                        this.props.dispatch(addSecurity(this.props.formId, {
                            ...data.security,
                            onSuccess: data => {
                                this.props.dispatch(removeSecurity(this.props.formId));
                                resolve(this._onSubmit({...values, ...data}));
                            },
                        }));
                    });
                }
                if (data.errors) {
                    throw new SubmissionError(data.errors);
                }
                if (!data.security) {
                    if (this.props.autoSave) {
                        AutoSaveHelper.remove(this.props.formId);
                    }
                    if (this.props.onComplete) {
                        this.props.onComplete(values, data);
                    }
                }
            });
    }

};
