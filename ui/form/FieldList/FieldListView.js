import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('FieldListView');

export default class FieldListView extends React.PureComponent {

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
        showAdd: PropTypes.bool,
        showRemove: PropTypes.bool,
        onAdd: PropTypes.func,
        renderField: PropTypes.func,
        disabled: PropTypes.bool,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <table
                    className={bem(
                        bem.element('table'),
                        'table',
                        this.props.size && 'table-' + this.props.size,
                    )}
                >
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
                        {this.props.children}
                    </tbody>
                </table>
                {this.props.showAdd && !this.props.disabled && (
                    <a
                        href='javascript:void(0)'
                        className={bem.element('link-add')}
                        onClick={this.props.onAdd}
                    >
                        {__('Добавить ещё')}
                    </a>
                )}
            </div>
        );
    }

}
