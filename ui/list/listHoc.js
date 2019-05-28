import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {getFormValues} from 'redux-form';
import _get from 'lodash-es/get';
import _has from 'lodash-es/has';
import _isEqual from 'lodash-es/isEqual';
import _isString from 'lodash-es/isString';
import _isFunction from 'lodash-es/isFunction';
import _isArray from 'lodash-es/isArray';
import _isEmpty from 'lodash-es/isEmpty';
import _mergeWith from 'lodash-es/mergeWith';

import {init, lazyFetch, fetch, setSort, destroy} from '../../actions/list';
import {getList} from '../../reducers/list';
import Empty from './Empty';
import Pagination from './Pagination';
import PaginationSize from './PaginationSize';
import Form from '../form/Form';
import {getMeta} from '../../reducers/fields';

let formValuesSelectors = {};
export const getFormId = props => _get(props, 'searchForm.formId', props.listId);

export default
() => WrappedComponent => @connect(
    (state, props) => {
        const formId = getFormId(props);
        if (formId && !formValuesSelectors[formId]) {
            formValuesSelectors[formId] = getFormValues(formId);
        }

        const list = getList(state, props.listId);

        let model = props.model || _get(list, 'meta.model');
        if (_isString(model)) {
            model = getMeta(state, model) || null;
        }

        let searchForm = props.searchForm;
        if (searchForm) {
            let searchModel = searchForm.model || _get(list, 'meta.searchModel');
            if (_isString(searchModel)) {
                searchForm.model = {
                    fields: {
                        ..._get(getMeta(state, searchModel), 'fields'),
                        ..._get(getMeta(state, searchModel), 'searchFields')
                    },
                    ...searchForm.model,
                };
                if (_isEmpty(searchForm.model)) {
                    searchForm.model = null;
                }
            }
        }

        return {
            model,
            searchForm,
            list,
            formValues: formId && formValuesSelectors[formId](state) || null,
        };
    }
)
    class ListHoc extends React.PureComponent {

        static WrappedComponent = WrappedComponent;

        /**
         * Proxy real name, prop types and default props for storybook
         */
        static displayName = WrappedComponent.displayName || WrappedComponent.name;

        static propTypes = {
            ...WrappedComponent.propTypes,
            listId: PropTypes.string.isRequired,
            primaryKey: PropTypes.string,
            action: PropTypes.string,
            actionMethod: PropTypes.string,
            scope: PropTypes.arrayOf(PropTypes.string),
            onFetch: PropTypes.func,
            loadMore: PropTypes.bool,
            defaultPage: PropTypes.number,
            defaultPageSize: PropTypes.number,
            defaultSort: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.arrayOf(PropTypes.string),
            ]),
            query: PropTypes.object,
            items: PropTypes.array,
            total: PropTypes.number,
            reverse: PropTypes.bool,
            itemsIndexing: PropTypes.bool,
            searchForm: PropTypes.shape({
                formId: PropTypes.string,
                prefix: PropTypes.string,
                model: PropTypes.oneOfType([
                    PropTypes.string,
                    PropTypes.func,
                    PropTypes.object,
                ]),
                layout: PropTypes.oneOfType([
                    PropTypes.oneOf(['default', 'inline', 'horizontal', 'table']),
                    PropTypes.string,
                ]),
                layoutProps: PropTypes.object,
                initialValues: PropTypes.object,
                fields: PropTypes.arrayOf(PropTypes.oneOfType([
                    PropTypes.string,
                    PropTypes.shape({
                        label: PropTypes.string,
                        hint: PropTypes.string,
                        required: PropTypes.bool,
                        component: PropTypes.oneOfType([
                            PropTypes.string,
                            PropTypes.func,
                        ]),
                    })
                ])),
            }),
            model: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
                PropTypes.object,
            ]),
            emptyText: PropTypes.string,
            emptyView: PropTypes.func,
            emptyProps: PropTypes.object,
            paginationView: PropTypes.oneOfType([
                PropTypes.func,
                PropTypes.bool,
            ]),
            paginationProps: PropTypes.object,
            paginationSizeView: PropTypes.oneOfType([
                PropTypes.func,
                PropTypes.bool,
            ]),
            paginationSizeProps: PropTypes.object,
            list: PropTypes.shape({
                meta: PropTypes.object,
                isFetched: PropTypes.bool,
                isLoading: PropTypes.bool,
                page: PropTypes.number,
                pageSize: PropTypes.number,
                total: PropTypes.number,
                sort: PropTypes.oneOfType([
                    PropTypes.string,
                    PropTypes.arrayOf(PropTypes.string),
                ]),
                query: PropTypes.object,
                items: PropTypes.array,
            }),
        };

        static defaultProps = {
            ...WrappedComponent.defaultProps,
            actionMethod: 'post',
            paginationSizeView: false,
            primaryKey: 'id',
            defaultPage: 1,
            defaultPageSize: 20,
            loadMore: true,
            reverse: false,
        };

        constructor() {
            super(...arguments);

            this._onFetch = this._onFetch.bind(this);
            this._onSort = this._onSort.bind(this);
        }

        componentDidMount() {
            this.props.dispatch(init(this.props.listId, this.props));
        }

        componentWillReceiveProps(nextProps) {
            const customizer = (objValue, srcValue) => {
                if (_isArray(objValue)) {
                    return srcValue;
                }
            };
            const getQuery = props => {
                const query = _get(props, 'list.query') || {};
                const formValues = _get(props, 'formValues') || {};

                Object.keys(query).forEach(attribute => {
                    if (!_has(formValues, attribute)) {
                        formValues[attribute] = null;
                    }
                });

                return _mergeWith({}, query, formValues, customizer)
            };

            // Send fetch request on change query or init list

            const prevQuery = getQuery(this.props);
            const nextQuery = getQuery(nextProps);
            if (!_isEqual(prevQuery, nextQuery) || (!this.props.list && nextProps.list)) {
                this.props.dispatch(lazyFetch(this.props.listId, {
                    page: this.props.defaultPage,
                    query: nextQuery,
                }));
            }

            if (this.props.items !== nextProps.items) {
                this.props.dispatch(init(this.props.listId, nextProps));
            }
        }

        componentWillUnmount() {
            this.props.dispatch(destroy(this.props.listId));
        }

        render() {
            // Check is init
            if (!this.props.list) {
                return null;
            }

            // Reverse items, if need
            let items = _get(this.props, 'list.items') || [];
            if (this.props.reverse) {
                items = [].concat(items).reverse();
            }

            // Set "index" property to items depending on page number
            if (this.props.itemsIndexing) {
                items = [].concat(items.map(item => ({
                    ...item,
                    index: items.findIndex(searchItem => searchItem.id === item.id)
                        + (this.props.list.page > 1 && ((this.props.list.page - 1) * this.props.list.pageSize))
                        + 1, // + 1 to start indexing elements from 1 insted of 0.
                })))
            }

            // Customize model from backend
            const searchForm = this.props.searchForm
                ? {
                    ...this.props.searchForm,
                    model: this.props.searchForm.model || _get(this.props.list, 'meta.searchModel')
                }
                : null;

            return (
                <WrappedComponent
                    {...this.props}
                    searchForm={searchForm}
                    isLoading={_get(this.props, 'list.isLoading')}
                    items={items}
                    empty={this.renderEmpty()}
                    pagination={this.renderPagination()}
                    paginationSize={this.renderPaginationSize()}
                    outsideSearchForm={this.renderOutsideSearchForm()}
                    fetch={this._onFetch}
                    sort={this._onSort}
                />
            );
        }

        renderEmpty() {
            if (this.props.list.isLoading) {
                return null;
            }
            if (!this.props.list.items || this.props.list.items.length > 0) {
                return null;
            }

            return (
                <Empty
                    text={this.props.emptyText}
                    {...this.props}
                    {...this.props.emptyProps}
                    view={this.props.emptyView}
                />
            );
        }

        renderPagination() {
            if (this.props.paginationView === false) {
                return null;
            }
            if (!this.props.list.items || this.props.list.total <= this.props.list.pageSize) {
                return null;
            }

            return (
                <Pagination
                    {...this.props}
                    {...this.props.paginationProps}
                    view={_isFunction(this.props.paginationSizeView) ? this.props.paginationSizeView : undefined}
                />
            );
        }

        renderPaginationSize() {
            if (this.props.paginationSizeView === false) {
                return null;
            }
            if (!this.props.list.items || this.props.list.items.length === 0) {
                return null;
            }

            return (
                <div>
                    <PaginationSize
                        {...this.props}
                        {...this.props.paginationSizeProps}
                        view={_isFunction(this.props.paginationSizeView) ? this.props.paginationSizeView : undefined}
                    />
                </div>
            );
        }

        renderOutsideSearchForm() {
            if (!this.props.searchForm || !this.props.searchForm.fields) {
                return null;
            }
            if (this.props.searchForm.layout === 'table') {
                return null;
            }
            if ((this.props.scope || []).includes('model') && !this.props.list.isFetched) {
                return null;
            }

            return (
                <Form
                    submitLabel={__('Найти')}
                    {...this.props.searchForm}
                    model={_get(this.props.searchForm, 'model') || _get(this.props.list, 'meta.searchModel')}
                    formId={getFormId(this.props)}
                    onSubmit={() => this._onFetch()}
                />
            );
        }

        _onFetch(params) {
            this.props.dispatch(fetch(this.props.listId, params));
        }

        _onSort(sort) {
            this.props.dispatch(setSort(this.props.listId, sort));
        }

};
