import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('FieldListItemView');

export default class FieldListItemView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        required: PropTypes.bool,
        items: PropTypes.arrayOf(PropTypes.shape({
            label: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.bool,
                PropTypes.element,
            ]),
            hint: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.bool,
                PropTypes.element,
            ]),
            attribute: PropTypes.string,
            prefix: PropTypes.string,
            model: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
                PropTypes.object,
            ]),
            component: PropTypes.any,
            required: PropTypes.bool,
            size: PropTypes.oneOf(['sm', 'md', 'lg']),
            placeholder: PropTypes.string,
            disabled: PropTypes.bool,
            onChange: PropTypes.func,
            className: PropTypes.string,
            headerClassName: PropTypes.string,
            view: PropTypes.func,
        })),
        showRemove: PropTypes.bool,
        onRemove: PropTypes.func,
        renderField: PropTypes.func,
        disabled: PropTypes.bool,
        className: PropTypes.string,
        prefix: PropTypes.string,
        rowIndex: PropTypes.number,
    };

    render() {
        return (
            <tr>
                {this.props.items.map((field, index) => (
                    <td
                        key={index}
                        className={bem(
                            bem.element('table-cell'),
                            field.className
                        )}
                    >
                        {this.props.renderField(field, this.props.prefix)}
                    </td>
                ))}
                {this.props.showRemove && (
                    <td className={bem.element('table-cell', 'remove')}>
                        {(!this.props.required || this.props.rowIndex > 0) && (
                            <div
                                className={bem.element('remove')}
                                onClick={() => this.props.onRemove(this.props.rowIndex)}
                            >
                                &times;
                            </div>
                        )}
                    </td>
                )}
            </tr>
        );
    }

}
