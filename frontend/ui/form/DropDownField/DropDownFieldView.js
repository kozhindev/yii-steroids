import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('DropDownFieldView');
import './DropDownFieldView.scss'

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
            <div className={bem.block()}>
                <div
                    className={bem.element('selected-items', {reset: this.props.showReset})}
                    onClick={this.props.onOpen}
                >
                    {this.props.selectedItems.map(item => (
                        <span key={item.id}>
                            {item.label} &nbsp;
                        </span>
                    ))}
                    {this.props.showReset && !!this.props.selectedItems.length && (
                        <span
                            className={bem.element('reset')}
                            onClick={this.props.onReset}
                        />
                    )}
                </div>
                {this.props.isOpened && (
                    <div className={bem.element('drop-down')}>
                        {this.props.autoComplete && (
                            <div className={bem.element('search')}>
                                <input
                                    {...this.props.searchInputProps}
                                    className={bem(bem.element('search-input'), 'form-control')}
                                />
                            </div>
                        )}
                        <div className={bem.element('drop-down-list')}>
                            {this.props.items.map(item => (
                                <div
                                    key={item.id}
                                    className={bem.element('drop-down-item', {hover: item.isHovered, select: item.isSelected})}
                                    onClick={() => this.props.onItemClick(item)}
                                    onMouseOver={() => this.props.onItemMouseOver(item)}
                                >

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
