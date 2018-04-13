import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {getFormValues} from 'redux-form';
import _get from 'lodash-es/get';
import _isEqual from 'lodash-es/isEqual';
import _merge from 'lodash-es/merge';

import {locale} from 'components';
import {init, lazyFetch, refresh, destroy} from '../../actions/list';
import {getList} from '../../reducers/list';
import Empty from './Empty';
import Pagination from './Pagination';
import PaginationSize from './PaginationSize';
import Form from '../form/Form';

let formValuesSelectors = {};
const getFormId = props => _get(props, 'searchForm.formId', props.listId);

@connect(
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
    export default () => WrappedComponent => class ListHoc extends React.PureComponent {

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
        loadMore: PropTypes.bool,
        defaultPageSize: PropTypes.number,
        defaultSort: PropTypes.object,
        query: PropTypes.object,
        items: PropTypes.array,
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
        let items = _get(this.props, 'list.items', []);
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
        if (!this.props.list.items || this.props.list.items.length === 0) {
            return null;
        }

        return (
            <Empty
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
                view={this.props.paginationView}
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
            <PaginationSize
                {...this.props}
                {...this.props.paginationSizeProps}
                view={this.props.paginationSizeView}
            />
        );
    }

    renderSearchForm() {
        if (!this.props.searchForm) {
            return null;
        }

        return (
            <Form
                submitLabel={locale.t('Найти')}
                {...this.props.searchForm}
                formId={getFormId(this.props)}
                onSubmit={() => this.props.dispatch(refresh())}
            />
        );
    }

};