import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import CheckboxField from '../../form/CheckboxField';

const bem = html.bem('CheckboxColumnView');

export default class CheckboxColumnView extends React.PureComponent {

    static propTypes = {
        input: PropTypes.object,
        fieldProps: PropTypes.object,
        isChecked: PropTypes.bool,
    };

    render() {
        const CheckboxFieldInternal = CheckboxField.WrappedComponent;
        return (
            <div className={bem.block()}>
                <CheckboxFieldInternal
                    {...this.props.fieldProps}
                    input={this.props.input}
                />
            </div>
        );
    }
}
