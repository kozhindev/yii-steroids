import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';
import fieldHoc from '../fieldHoc';

@fieldHoc({
    layoutProps: {
        label: false,
    }
})
export default class CheckboxField extends React.PureComponent {

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
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    static defaultProps = {
        disabled: false,
        size: 'md',
        className: '',
    };

    render() {
        const CheckboxFieldView = this.props.view || view.get('form.CheckboxFieldView');
        return (
            <CheckboxFieldView
                {...this.props}
                inputProps={{
                    name: this.props.input.name,
                    type: 'checkbox',
                    checked: !!this.props.input.value,
                    onChange: e => this.props.input.onChange(!this.props.input.value),
                    disabled: this.props.disabled,
                    ...this.props.inputProps,
                }}
            />
        );
    }

}
