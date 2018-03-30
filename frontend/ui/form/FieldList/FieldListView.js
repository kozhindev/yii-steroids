import React from 'react';
import PropTypes from 'prop-types';

import {html, locale} from 'components';

const bem = html.bem('FieldListView');

export default class FieldListView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        rows: PropTypes.arrayOf(PropTypes.string),
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
        showAdd: PropTypes.bool,
        showRemove: PropTypes.bool,
        onAdd: PropTypes.func,
        onRemove: PropTypes.func,
        renderField: PropTypes.func,
        disabled: PropTypes.bool,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <table className={bem(bem.element('table'), 'table')}>
                    <thead>
                        <tr>
                            {this.props.items.map((field, rowIndex) => (
                                <th
                                    key={rowIndex}
                                    className={bem(
                                        bem.element('table-cell-header'),
                                        field.headerClassName
                                    )}
                                >
                                    {field.label}
                                </th>
                            ))}
                            {this.props.showRemove && (
                                <th/>
                            )}
                        </tr>
                    </thead>
                    <tbody>
                        {this.props.rows.map((prefix, rowIndex) => (
                            <tr key={rowIndex}>
                                {this.props.items.map((field, columnIndex) => (
                                    <td
                                        key={`${rowIndex}_${columnIndex}`}
                                        className={bem(
                                            bem.element('table-cell'),
                                            field.className
                                        )}
                                    >
                                        {this.props.renderField(field, prefix)}
                                    </td>
                                ))}
                                {this.props.showRemove && (
                                    <td className={bem.element('table-cell', 'remove')}>
                                        {(!this.props.required || rowIndex > 0) && (
                                            <div
                                                className={bem.element('remove')}
                                                onClick={() => this.props.onRemove(rowIndex)}
                                            >
                                                &times;
                                            </div>
                                        )}
                                    </td>
                                )}
                            </tr>
                        ))}
                    </tbody>
                </table>
                {this.props.showAdd && !this.props.disabled && (
                    <a
                        href='javascript:void(0)'
                        className={bem.element('link-add')}
                        onClick={this.props.onAdd}
                    >
                        {locale.t('Добавить ещё')}
                    </a>
                )}
            </div>
        );
    }

}
