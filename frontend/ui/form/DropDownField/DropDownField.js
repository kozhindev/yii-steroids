import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {submit} from 'redux-form';

import {view} from 'components';
import fieldHoc from '../fieldHoc';

@fieldHoc()
export default class DropDownField extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        attribute: PropTypes.string,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        required: PropTypes.bool,
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,

        // dataProvider
            // ArrayDataProvider | array
            //   items
            //   autoComplete
            //   minLength
            //   delay
            // EnumDataProvider
            //   enumClassName string|func
            //   autoComplete
            //   minLength
            //   delay
            // RemoteDataProvider
            //   action
            //   params
            //     modelClass
            //   autoFetch
            //   autoComplete
            //   minLength
            //   delay
        searchPlaceholder: PropTypes.string,
        multiple: PropTypes.bool,
        autoSelectFirst: PropTypes.bool,
    };

    static defaultProps = {
        disabled: false,
    };

    constructor() {
        super(...arguments);

        this._onKeyUp = this._onKeyUp.bind(this);
    }

    render() {
        const DropDownFieldView = this.props.view || view.get('form.DropDownFieldView');
        return (
            <DropDownFieldView
                {...this.props}
                inputProps={{
                    name: this.props.input.name,
                    value: this.props.input.value || '',
                    onChange: e => this.props.input.onChange(e.target.value),
                    onKeyUp: this._onKeyUp,
                    placeholder: this.props.placeholder,
                    disabled: this.props.disabled,
                    ...this.props.inputProps,
                }}
            />
        );
    }

    _onKeyUp(e) {
        if (this.props.submitOnEnter && this.props.formId && e.keyCode === 13 && !e.shiftKey) {
            e.preventDefault();

            // TODO This is not worked in redux... =(
            this.props.dispatch(submit(this.props.formId));
        }
    }

}
