import React from 'react';
import {connect} from 'react-redux';
import PropTypes from 'prop-types';
import {change} from 'redux-form';
import _remove from 'lodash-es/remove';
import _some from 'lodash-es/some';
import _has from 'lodash-es/has';
import _isString from 'lodash-es/isString';
import _isArray from 'lodash-es/isArray';
import _isFunction from 'lodash-es/isFunction';
import _isObject from 'lodash-es/isObject';
import _includes from 'lodash-es/includes';
import _uniqBy from 'lodash-es/uniqBy';
import _isInteger from 'lodash-es/isInteger';
import _orderBy from 'lodash-es/orderBy';

import {http} from 'components';
import {getEnumLabels} from '../../reducers/fields';

const stateMap = (state, props) => ({
    items: _isString(props.items)
        ? getEnumLabels(state, props.items)
        : props.items,
});

export default () => WrappedComponent => @connect(stateMap)
class DataProviderHoc extends React.PureComponent {

    static WrappedComponent = WrappedComponent;

    /**
     * Proxy real name, prop types and default props for storybook
     */
    static displayName = WrappedComponent.displayName || WrappedComponent.name;

    static propTypes = {
        ...WrappedComponent.propTypes,
        multiple: PropTypes.bool,
        items: PropTypes.oneOfType([
            PropTypes.arrayOf(PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.number,
                PropTypes.shape({
                    id: PropTypes.oneOfType([
                        PropTypes.number,
                        PropTypes.string,
                        PropTypes.bool,
                    ]),
                    label: PropTypes.oneOfType([
                        PropTypes.string,
                        PropTypes.any,
                    ]),
                })
            ])),
            PropTypes.string, // Enum from redux state
            PropTypes.func, // Enum
        ]),
        dataProvider: PropTypes.shape({
            action: PropTypes.string,
            params: PropTypes.object,
            onSearch: PropTypes.func,
        }),
        autoComplete: PropTypes.bool,
        autoCompleteMinLength: PropTypes.number,
        autoCompleteDelay: PropTypes.number,
        autoFetch: PropTypes.bool,
        selectFirst: PropTypes.bool,
        onSelect: PropTypes.func,
    };

    static defaultProps = {
        ...WrappedComponent.defaultProps,
        autoCompleteMinLength: 2,
        autoCompleteDelay: 100,
    };

    /**
     * Normalize items for save to state. Support enum class or normal items list.
     * @param {array|object} items
     * @returns {*}
     */
    static normalizeItems(items) {
        // Array
        if (_isArray(items)) {
            // List of strings/numbers
            if (_some(items, item => _isString(item) || _isInteger(item))) {
                return items.map(item => {
                    if (_isString(item) || _isInteger(item)) {
                        return {
                            id: item,
                            label: item,
                        };
                    }
                    return item;
                });
            }

            // Labels as ids
            if (_some(items, item => !_has(item, 'id'))) {
                return _uniqBy(
                    items.map(item => {
                        return {
                            id: item.label,
                            ...item,
                        };
                    }),
                    'label'
                );
            }
            return items;
        }

        // Enum
        if (_isObject(items) && _isFunction(items.getLabels)) {
            const labels = items.getLabels();
            return Object.keys(labels).map(id => ({
                id,
                label: labels[id],
            }));
        }

        return [];
    }

    constructor() {
        super(...arguments);

        this._onOpen = this._onOpen.bind(this);
        this._onClose = this._onClose.bind(this);
        this._onSearch = this._onSearch.bind(this);
        this._onItemClick = this._onItemClick.bind(this);
        this._onItemMouseOver = this._onItemMouseOver.bind(this);
        this._onKeyDown = this._onKeyDown.bind(this);

        this._delayTimer = null;

        const sourceItems = DataProviderHoc.normalizeItems(this.props.items);
        this.state = {
            query: '',
            isOpened: false,
            isFocused: false,
            isLoading: false,
            hoveredItem: null,
            selectedItems: this._findSelectedItems(sourceItems, this.props.input.value),
            sourceItems,
            items: sourceItems,
        };
    }

    UNSAFE_componentWillMount() {
        // Select first value on mount
        if (this.props.selectFirst && this.state.items.length > 0) {
            this._onItemClick(this.state.items[0]);
        }

        // Check to auto fetch items first page
        if (this.props.autoFetch && this.props.dataProvider) {
            this._searchDataProvider('', true);
        }

        // Async load selected labels from backend
        // TODO
        /*if (values.length > 0 && !this.getLabel()) {
            this.props.dispatch(fetchByIds(this.props.fieldId, values, {
                model: this.props.modelClass,
                attribute: this.props.attribute,
            }));
        }*/
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        // Refresh normalized source items on change items from props
        if (this.props.items !== nextProps.items) {
            const sourceItems = DataProviderHoc.normalizeItems(nextProps.items);
            this.setState({
                sourceItems,
                items: sourceItems,
            });

            // Select first value on fetch data
            if ((this.props.items || []).length === 0 && (nextProps.items || []).length > 0 && this.props.selectFirst) {
                this._onItemClick(sourceItems[0]);
            }
        }

        // Store selected items in state on change value
        if (this.props.input.value !== nextProps.input.value) {
            const sourceItems = DataProviderHoc.normalizeItems(nextProps.items);
            this.setState({
                selectedItems: this._findSelectedItems(
                    _uniqBy([].concat(sourceItems, this.state.items, this.state.sourceItems, this.state.selectedItems), 'id'),
                    nextProps.input.value
                ),
            });
        }

        // Check auto fetch on change autoFetch flag or data provider config
        if (nextProps.autoFetch && nextProps.dataProvider && (!this.props.autoFetch || this.props.dataProvider !== nextProps.dataProvider)) {
            this._searchDataProvider('');
        }
    }

    componentDidMount() {
        window.addEventListener('keydown', this._onKeyDown);
    }

    componentWillUnmount() {
        window.removeEventListener('keydown', this._onKeyDown);
    }

    render() {
        return (
            <WrappedComponent
                {...this.props}
                selectedItems={this.state.selectedItems}
                hoveredItem={this.state.hoveredItem}
                isOpened={this.state.isOpened}
                isLoading={this.state.isLoading}
                items={this.state.items}
                onOpen={this._onOpen}
                onClose={this._onClose}
                onSearch={this._onSearch}
                onItemClick={this._onItemClick}
                onItemMouseOver={this._onItemMouseOver}
            />
        );
    }

    /**
     * Get items by values
     * @param {array} items
     * @param {array|string} value
     * @returns {array}
     * @private
     */
    _findSelectedItems(items, value) {
        const selectedValues = value === false || value === 0 ? [value] : [].concat(value || []);
        return items.filter(item => _includes(selectedValues, item.id));
    }

    /**
     * Handler for user open items dropdown menu
     * @private
     */
    _onOpen() {
        this.setState({
            isOpened: !this.state.isOpened,
            items: this.state.sourceItems,
            hoveredItem: null,
        });
    }

    /**
     * Handler for user close items dropdown menu
     * @private
     */
    _onClose() {
        this.setState({
            isOpened: false,
        });
    }

    /**
     * Handler for user auto complete search by key down events
     * @param {string} query
     * @private
     */
    _onSearch(query) {
        query = query || '';

        this.setState({query});

        if (this.props.dataProvider) {
            if (this._delayTimer) {
                clearTimeout(this._delayTimer);
            }

            // Min length query logic
            if (query.length >= this.props.autoCompleteMinLength) {
                // Search with delay
                this._delayTimer = setTimeout(() => this._searchDataProvider(query), this.props.autoCompleteDelay);
            }
        } else {
            // Client-side search on static items
            this._searchClientSide(query);
        }
    }

    /**
     * Client-side search on static items
     * @param {string} query
     * @private
     */
    _searchClientSide(query) {
        if (!query) {
            this.setState({
                items: this.state.sourceItems,
            });
            return;
        }

        const toWords = str => {
            let words = [];
            String(str).split(' ').forEach(strItem => {
                words = words.concat(strItem.match(/^[^\p{Lu}]+/u));
                words = words.concat(strItem.match(/[\p{Lu}][^\p{Lu}]*/gu));
                words.push(' ');
            });
            words.pop();
            return words.filter(Boolean);
        };
        const queryCharacters = query.split('');

        // Match
        let items = this.state.sourceItems.filter(item => {
            const id = item.id;
            const words = toWords(item.label || '');
            if (words.length === 0 || !id) {
                return false;
            }

            let word = null;
            let highlighted = [['', false]];
            let index = 0;
            let wordIndex = 0;
            let wordChar = null;
            let wordCharIndex = 0;
            while(true) {
                const char = queryCharacters[index];
                if (!char) {
                    highlighted.push([word.substr(wordCharIndex) + words.slice(wordIndex + 1).join(''), false]);
                    break;
                }

                word = words[wordIndex];
                wordChar = word && word.split('')[wordCharIndex] || '';
                if (!word) {
                    highlighted = [];
                    break;
                }

                const isMatch = wordChar.toLowerCase() === char.toLowerCase();

                if (isMatch) {
                    index++;
                    wordCharIndex++;
                    highlighted[highlighted.length - 1][0] += wordChar;
                    highlighted[highlighted.length - 1][1] = true;
                } else {
                    highlighted.push([word.substr(wordCharIndex), false]);
                    highlighted.push(['', false]);

                    wordIndex++;
                    wordCharIndex = 0;
                }
            }
            highlighted = highlighted.filter(item => !!item[0]);
            if (highlighted.findIndex(item => item[1]) !== -1) {
                item.labelHighlighted = highlighted;
                return true;
            }

            return false;
        });

        items = _orderBy(items, item => {
            // Fined first word is priority
            if (item.labelHighlighted) {
                return item.labelHighlighted.findIndex(i => i[1]);
            }
            return Infinity;
        }, 'asc');

        this.setState({items});
    }

    /**
     * Search by data provider (for example: http requests)
     * @param {string} query
     * @param {boolean} isAutoFetch
     * @private
     */
    _searchDataProvider(query = '', isAutoFetch) {
        if (!isAutoFetch && query.length < this.props.autoCompleteMinLength) {
            return;
        }

        const searchHandler = this.props.dataProvider.onSearch || http.post.bind(http);
        const result = searchHandler(this.props.dataProvider.action, {
            query,
            model: this.props.modelClass,
            attribute: this.props.attribute,
            ...this.props.dataProvider.params,
        });

        // Check is promise
        if (result && _isFunction(result.then)) {
            this.setState({isLoading: true});
            result.then(items =>  {
                items = DataProviderHoc.normalizeItems(items);
                this.setState({
                    isLoading: false,
                    items,
                    sourceItems: isAutoFetch ? items : this.state.sourceItems,
                });
            });
        }

        // Check is items list
        if (_isArray(result)) {
            const items = DataProviderHoc.normalizeItems(result);
            this.setState({
                items,
                sourceItems: isAutoFetch ? items : this.state.sourceItems,
            });
        }
    }

    /**
     * Handler for user click on item
     * @param {object} item
     * @param {boolean} skipToggle
     * @private
     */
    _onItemClick(item, skipToggle = false) {
        const id = item.id;

        if (this.props.multiple) {
            const values = [].concat(this.props.input.value || []);
            if (values.indexOf(id) !== -1) {
                if (!skipToggle) {
                    _remove(values, value => value === id);
                }
            } else {
                values.push(id);
            }
            this.props.input.onChange(values);

            // Fix bug. Without this calls component Form is not get differect values
            // in componentWillReceiveProps and onChange handlers is not called.
            if (this.props.formId) {
                this.props.dispatch(change(this.props.formId, this.props.input.name, values));
            }
        } else {
            if (this.props.input.value !== id) {
                this.props.input.onChange(id);
            }
            this._onClose();
        }

        if (this.props.onSelect) {
            this.props.onSelect(item, {
                prefix: this.props.prefix,
                name: this.props.input.name,
            });
        }
    }

    /**
     * Handler for user mouse over on item
     * @param {object} item
     * @private
     */
    _onItemMouseOver(item) {
        this.setState({
            hoveredItem: item,
        });
    }

    /**
     * Global key down handler for navigate on items
     * Support keys:
     *  - tab
     *  - esc
     *  - enter
     *  - up/down arrows
     * @param {object} e
     * @private
     */
    _onKeyDown(e) {
        if (!this.state.isFocused && !this.state.isOpened) {
            return;
        }

        switch (e.which) {
            case 9: // tab
            case 27: // esc
                e.preventDefault();
                this._onClose();
                break;

            case 13: // enter
                if (this.state.isOpened) {
                    e.preventDefault();

                    if (this.state.hoveredItem) {
                        // Select hovered
                        this._onItemClick(this.state.hoveredItem, true);
                    } else if (this.state.selectedItems.length > 0) {
                        // Select first selected
                        this._onItemClick(this.state.selectedItems[0], true);
                    } else if (this.state.items.length > 0) {
                        // Select first result
                        this._onItemClick(this.state.items[0], true);
                    }
                }
                break;

            case 38: // arrow up
            case 40: // arrow down
                e.preventDefault();

                const isDown = e.which === 40;
                if (!this.state.isOpened) {
                    // Open on down key
                    if (isDown) {
                        this._onOpen();
                    }
                    break;
                }

                // Navigate on items by keys
                const direction = isDown > 0 ? 1 : -1;
                const keys = this.state.items.map(item => item.id);
                let index = this.state.hoveredItem ? keys.indexOf(this.state.hoveredItem.id) : -1;
                if (index === -1 && this.state.selectedItems.length === 1) {
                    index = keys.indexOf(this.state.selectedItems[0].id);
                }

                const newIndex = index !== -1 ? Math.min(keys.length - 1, Math.max(0, index + direction)) : 0;
                this.setState({
                    hoveredItem: this.state.items.find(item => item.id === keys[newIndex]),
                });
                break;
        }
    }

};
