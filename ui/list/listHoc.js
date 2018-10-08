import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {getFormValues} from 'redux-form';
import _get from 'lodash-es/get';
import _isEqual from 'lodash-es/isEqual';
import _isFunction from 'lodash-es/isFunction';
import _merge from 'lodash-es/merge';

import {init, lazyFetch, refresh, destroy} from '../../actions/list';
import {getList} from '../../reducers/list';
import Empty from './Empty';
import Pagination from './Pagination';
import PaginationSize from './PaginationSize';
import Form from '../form/Form';

let formValuesSelectors = {};
const getFormId = props => _get(props, 'searchForm.formId', props.listId);

export default () => WrappedComponent => @connect(
    (state, props) => {
        const formId = getFormId(props);
        if (formId && !formValuesSelectors[formId]) {
            formValuesSelectors[formId] = getFormValues(formId);
        }

        return {
            list: getList(state, props.listId),
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
        onFetch: PropTypes.func,
        loadMore: PropTypes.bool,
        defaultPageSize: PropTypes.number,
        defaultSort: PropTypes.object,
        query: PropTypes.object,
        items: PropTypes.array,
        total: PropTypes.number,
        reverse: PropTypes.bool,
        searchForm: PropTypes.shape({
            formId: PropTypes.string,
            prefix: PropTypes.string,
            model: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
            ]),
            layout: PropTypes.oneOf(['default', 'inline', 'horizontal']),
            layoutProps: PropTypes.object,
            initialValues: PropTypes.object,
            fields: PropTypes.arrayOf(PropTypes.shape({
                label: PropTypes.string,
                hint: PropTypes.string,
                required: PropTypes.bool,
                component: PropTypes.oneOfType([
                    PropTypes.string,
                    PropTypes.func,
                ]),
            })),
        }),
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
            sort: PropTypes.object,
            query: PropTypes.object,
            items: PropTypes.array,
        }),
    };

    static defaultProps = {
        ...WrappedComponent.defaultProps,
        actionMethod: 'post',
        paginationSizeView: false,
        primaryKey: 'id',
        defaultPageSize: 20,
        loadMore: true,
        reverse: false,
    };

    componentDidMount() {
        this.props.dispatch(init(this.props.listId, this.props));
    }

    componentWillReceiveProps(nextProps) {
        // Send fetch request on change query or init list
        const prevQuery = _merge({}, _get(this.props, 'list.query'), _get(this.props, 'formValues'));
        const nextQuery = _merge({}, _get(nextProps, 'list.query'), _get(nextProps, 'formValues'));
        if (!_isEqual(prevQuery, nextQuery) || (!this.props.list && nextProps.list)) {
            this.props.dispatch(lazyFetch(this.props.listId, {
                page: 1,
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

        return (
            <WrappedComponent
                {...this.props}
                isLoading={_get(this.props, 'list.isLoading')}
                items={items}
                empty={this.renderEmpty()}
                pagination={this.renderPagination()}
                paginationSize={this.renderPaginationSize()}
                searchForm={this.renderSearchForm()}
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

    renderSearchForm() {
        if (!this.props.searchForm || !this.props.searchForm.fields) {
            return null;
        }

        return (
            <Form
                submitLabel={__('Найти')}
                {...this.props.searchForm}
                formId={getFormId(this.props)}
                onSubmit={() => this.props.dispatch(refresh())}
            />
        );
    }

};