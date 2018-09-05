import React from 'react';
import PropTypes from 'prop-types';

import {ui} from 'components';
import fieldHoc from '../fieldHoc';

export default
@fieldHoc({
    componentId: 'form.CheckboxField',
    layoutProps: {
        label: false,
    }
})
class CheckboxField extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        attribute: PropTypes.string,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        required: PropTypes.bool,
        isInvalid: PropTypes.bool,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    static defaultProps = {
        disabled: false,
        required: false,
        className: '',
        errors: [], //for storybook
    };

    render() {
        const CheckboxFieldView = this.props.view || ui.getView('form.CheckboxFieldView');
        return (
            <CheckboxFieldView
                {...this.props}
                inputProps={{
                    name: this.props.input.name,
                    type: 'checkbox',
                    checked: !!this.props.input.value,
                    onChange: () => this.props.input.onChange(!this.props.input.value),
                    disabled: this.props.disabled,
                    ...this.props.inputProps,
                }}
            />
        );
    }

}
