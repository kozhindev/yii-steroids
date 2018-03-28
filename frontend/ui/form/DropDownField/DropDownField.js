import React from 'react';
import PropTypes from 'prop-types';
import enhanceWithClickOutside from 'react-click-outside';

import {view, locale} from 'components';
import fieldHoc from '../fieldHoc';
import dataProviderHoc from '../dataProviderHoc';

@fieldHoc()
@dataProviderHoc()
@enhanceWithClickOutside
export default class DropDownField extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        attribute: PropTypes.string,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        required: PropTypes.bool,
        placeholder: PropTypes.string,
        searchPlaceholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
        showReset: PropTypes.bool,
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
        })),
        hoveredItem: PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
            ]),
            label: PropTypes.string,
        }),
        autoComplete: PropTypes.bool,
        autoCompleteMinLength: PropTypes.number,
        autoCompleteDelay: PropTypes.number,
        isOpened: PropTypes.bool,
        onOpen: PropTypes.func,
        onClose: PropTypes.func,
        onSearch: PropTypes.func,
        onItemClick: PropTypes.func,
        onItemMouseOver: PropTypes.func,
    };

    static defaultProps = {
        disabled: false,
        required: false,
        className: '',
        searchPlaceholder: locale.t('Поиск'),
        autoComplete: false,
        showReset: false,
        multiple: false,
    };

    constructor() {
        super(...arguments);

        this._onSearch = this._onSearch.bind(this);
        this._onReset = this._onReset.bind(this);
    }

    handleClickOutside() {
        this.props.onClose();
    }

    render() {
        const DropDownFieldView = this.props.view || view.get('form.DropDownFieldView');
        return (
            <DropDownFieldView
                {...this.props}
                searchInputProps={{
                    type: 'search',
                    placeholder: this.props.searchPlaceholder || locale.t('Начните вводить символы для поиска...'),
                    onChange: this._onSearch,
                    tabIndex: -1
                }}
                items={this.props.items.map(item => ({
                    ...item,
                    isSelected: !!this.props.selectedItems.find(selectedItem => selectedItem.id === item.id),
                    isHovered: this.props.hoveredItem && this.props.hoveredItem.id === item.id,
                }))}
                selectedItems={this.props.selectedItems}
                isOpened={this.props.isOpened}
                showReset={this.props.showReset}
                onOpen={this.props.onOpen}
                onReset={this._onReset}
                onItemClick={this.props.onItemClick}
                onItemMouseOver={this.props.onItemMouseOver}
            />
        );
    }

    _onSearch(e) {
        this.props.onSearch(e.target.value);
    }

    _onReset() {
        this.props.input.onChange(null);
    }


}
