import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import _upperFirst from 'lodash-es/upperFirst';

class FieldHoc extends React.PureComponent {

    constructor() {
        super(...arguments);

        if (!this.props.formId) {
            this.state = {
                value: _get(this.props, 'input.value'),
            };
        }
    }

    render() {
        const {_wrappedComponent, _config, ...props} = this.props;
        const WrappedComponent = _wrappedComponent;

        if (this.props.formId) {
            // <Field component=... />
        }

        const inputProps = {};
        if (_config.attributes) {
            _config.attributes.forEach(attribute => {
                inputProps[`input${_upperFirst(attribute)}`] = {
                    name: '',
                    value: _get(this.state.value, attribute),
                    onChange: value => this.setState({
                        value: {
                            ...this.state.value,
                            [attribute]: value,
                        }
                    }),
                };
            });
        } else {
            inputProps.input = {
                name: '',
                value: this.state.value,
                onChange: value => this.setState({value}),
            };
        }

        return (
            <WrappedComponent
                {...props}
                {...inputProps}
            />
        );
    }
}

export default (config = {}) => WrappedComponent => class FieldHocWrapper extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    static contextTypes = {
        formId: PropTypes.string,
    };

    render() {
        return (
            <FieldHoc
                {...this.props}
                formId={this.props.formId}
                _wrappedComponent={WrappedComponent}
                _config={config}
            />
        );
    }

};