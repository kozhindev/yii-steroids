import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('CheckboxListFieldView');

export default class CheckboxListFieldView extends React.PureComponent {

    static propTypes = {
        fieldId: PropTypes.string,
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        isInvalid: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        className: PropTypes.string,
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
                PropTypes.bool,
            ]),
            label: PropTypes.string,
        })),
        onItemClick: PropTypes.func,
    };

    render() {
        return (
            <div className={bem.block()}>
                {this.props.items.map(item => (
                    <div
                        key={item.id}
                        className='custom-control custom-checkbox'
                    >
                        <input
                            {...this.props.inputProps}
                            id={this.props.fieldId + '_' + item.id}
                            className={bem(
                                bem.element('input'),
                                'custom-control-input',
                                this.props.isInvalid && 'is-invalid',
                            )}
                            checked={item.isSelected}
                            disabled={this.props.disabled}
                            onChange={() => this.props.onItemClick(item)}
                        />
                        <label
                            className='custom-control-label'
                            htmlFor={this.props.fieldId + '_' + item.id}
                        >
                            {item.label}
                        </label>
                    </div>
                ))}
            </div>
        );
    }

}
