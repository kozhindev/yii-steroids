import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';
import fieldHoc from '../fieldHoc';

@fieldHoc({
    attributes: ['from', 'to'],
})
export default class RangeField extends React.Component {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        attributeFrom: PropTypes.string,
        attributeTo: PropTypes.string,
        inputFrom: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        inputTo: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        placeholderFrom: PropTypes.string,
        placeholderTo: PropTypes.string,
        disabled: PropTypes.bool,
        inputFromProps: PropTypes.object,
        inputToProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    static defaultProps = {
        disabled: false,
    };

    render() {
        const RangeFieldView = this.props.view || view.get('form.RangeFieldView');
        return (
            <RangeFieldView
                {...this.props}
                inputFromProps={{
                    name: this.props.inputFrom.name,
                    value: this.props.inputFrom.value || '',
                    onChange: e => this.props.inputFrom.onChange(e.target.value),
                    type: 'text',
                    placeholder: this.props.placeholderFrom,
                    disabled: this.props.disabled,
                    ...this.props.inputFromProps,
                }}
                inputToProps={{
                    name: this.props.inputTo.name,
                    value: this.props.inputTo.value || '',
                    onChange: e => this.props.inputTo.onChange(e.target.value),
                    type: 'text',
                    placeholder: this.props.placeholderTo,
                    disabled: this.props.disabled,
                    ...this.props.inputToProps,
                }}
            />
        );
    }

}
