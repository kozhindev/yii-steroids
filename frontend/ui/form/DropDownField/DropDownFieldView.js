import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('DropDownFieldView');

export default class DropDownFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        placeholder: PropTypes.string,
        searchPlaceholder: PropTypes.string,
        disabled: PropTypes.bool,
        className: PropTypes.string,
        searchInputProps: PropTypes.object,
        multiple: PropTypes.bool,
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
            ]),
            label: PropTypes.string,
        })),
        selectedItems: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
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
            <div>
                <div
                    className={bem.element('selected-items')}
                    onClick={this.props.onOpen}
                    style={{
                        width: 200,
                        height: 30,
                        border: 'solid 1px #eee',
                    }}
                >
                    {this.props.selectedItems.map(item => (
                        <span key={item.id}>
                            {item.label}
                        </span>
                    ))}
                </div>
                {this.props.isOpened && (
                    <div className={bem.element('drop-down')}>
                        {this.props.autoComplete && (
                            <input
                                {...this.props.searchInputProps}
                                className={bem(bem.element('search-input'), 'form-control')}
                            />
                        )}
                        <div className={bem.element('drop-down-list')}>
                            {this.props.items.map(item => (
                                <div
                                    key={item.id}
                                    className={bem.element('drop-down-item', {hover: item.isHovered})}
                                    onClick={() => this.props.onItemClick(item)}
                                    onMouseOver={() => this.props.onItemMouseOver(item)}
                                >
                                    {this.props.multiple && (
                                        <span>
                                            {item.isSelected ? '[x]' : '[ ]'}
                                        </span>
                                    )}
                                    {item.label}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        );
    }

}
