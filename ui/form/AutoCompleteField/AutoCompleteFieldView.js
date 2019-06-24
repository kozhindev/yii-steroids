import React from 'react';
import PropTypes from 'prop-types';

import {html, ui} from 'components';
const bem = html.bem('AutoCompleteFieldView');
import './AutoCompleteFieldView.scss';

export default class AutoCompleteFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        placeholder: PropTypes.string,
        isInvalid: PropTypes.bool,
        searchPlaceholder: PropTypes.string,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        className: PropTypes.string,
        inputProps: PropTypes.object,
        multiple: PropTypes.bool,
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
                PropTypes.bool,
            ]),
            label: PropTypes.string,
            labelHighlighted: PropTypes.array,
        })),
        selectedItems: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
                PropTypes.bool,
            ]),
            label: PropTypes.string,
            isSelected: PropTypes.bool,
            isHovered: PropTypes.bool,
        })),
        autoComplete: PropTypes.bool,
        autoCompleteMinLength: PropTypes.number,
        autoCompleteDelay: PropTypes.number,
        isOpened: PropTypes.bool,
        showReset: PropTypes.bool,
        onOpen: PropTypes.func,
        onReset: PropTypes.func,
        onItemClick: PropTypes.func,
        onItemMouseOver: PropTypes.func,
    };

    render() {
        return (
            <div className={bem.block({size: this.props.size})}>
                <input
                    className={bem(
                        bem.block({
                            size: this.props.size,
                        }),
                        'form-control',
                        'form-control-' + this.props.size,
                        this.props.isInvalid && 'is-invalid',
                        this.props.className
                    )}
                    {...this.props.inputProps}
                    placeholder={this.props.placeholder}
                    disabled={this.props.disabled}
                    required={this.props.required}
                />
                {this.props.isOpened && (
                    <div className={bem.element('drop-down')}>
                        <div className={bem.element('drop-down-list')}>
                            {this.props.items.map(item => (
                                <div
                                    key={item.id}
                                    className={bem.element('drop-down-item', {hover: item.isHovered, select: item.isSelected})}
                                    onClick={() => this.props.onItemClick(item)}
                                    onMouseOver={() => this.props.onItemMouseOver(item)}
                                >
                                    {item.labelHighlighted && (
                                        item.labelHighlighted.map((item, index) => (
                                            item[1]
                                                ? <b key={index}>{item[0]}</b>
                                                : <span key={index}>{item[0]}</span>
                                        ))
                                    ) || item.label}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        );
    }

}
