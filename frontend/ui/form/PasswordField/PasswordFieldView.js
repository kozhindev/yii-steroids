import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('NumberFieldView');

export default class NumberFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        security: PropTypes.bool,
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
        securityLevel: PropTypes.string,
        onShowPassword: PropTypes.func,
        onHidePassword: PropTypes.func,
    };

    render() {
        return (
            <div>
                <input
                    className={bem(bem.block(), 'form-control', this.props.className)}
                    {...this.props.inputProps}
                />
                {this.props.security && (
                    <div>
                        {this.props.securityLevel}
                        &nbsp;
                        <a
                            href='javascript:void(0)'
                            onMouseDown={this.props.onShowPassword}
                            onMouseUp={this.props.onHidePassword}
                        >
                            show
                        </a>
                    </div>
                )}
            </div>
        );
    }

}
