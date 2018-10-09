import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import _get from 'lodash-es/get';

import {ui} from 'components';
import {setPage} from '../../../actions/list';

export default
@connect()
class Pagination extends React.PureComponent {

    static propTypes = {
        listId: PropTypes.string,
        loadMore: PropTypes.bool,
        aroundCount: PropTypes.number,
        list: PropTypes.shape({
            page: PropTypes.number,
            pageSize: PropTypes.number,
            total: PropTypes.number,
        }),
        className: PropTypes.string,
        view: PropTypes.func,
        pageParam: PropTypes.string,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
    };

    static defaultProps = {
        aroundCount: 3,
        size: 'md',
    };

    constructor() {
        super(...arguments);

        this._onSelect = this._onSelect.bind(this);
        this._onSelectNext = this._onSelectNext.bind(this);
    }

    render() {
        const page = _get(this.props, 'list.page', 1);
        const totalPages = Math.ceil(_get(this.props, 'list.total', 0) / _get(this.props, 'list.pageSize', 0));

        // Do not show in last page in 'loadMore' mode
        if (this.props.loadMore && page >= totalPages) {
            return null;
        }

        const PaginationView = this.props.view || (this.props.loadMore ? ui.getView('list.PaginationMoreView') : ui.getView('list.PaginationButtonView'));
        return (
            <PaginationView
                {...this.props}
                page={page}
                totalPages={totalPages}
                pages={this.generatePages(page, totalPages).map(page => ({
                    page: page !== '...' ? page : null,
                    label: page,
                    isActive: _get(this.props, 'list.page') === page,
                }))}
                onSelect={this._onSelect}
                onSelectNext={this._onSelectNext}
            />
        );
    }

    generatePages(page, totalPages) {
        const pages = [];

        for (let i = 1; i <= totalPages; i++) {
            // Store first and last
            if (i === 1 || i === totalPages) {
                pages.push(i);
                continue;
            }

            // Store around
            if (page - this.props.aroundCount < i && i < page + this.props.aroundCount) {
                pages.push(i);
                continue;
            }

            if (pages[pages.length - 1] !== '...') {
                pages.push('...');
            }
        }

        return pages;
    }

    _onSelect(page) {
        if (page) {
            if (this.props.pageParam) {
                // TODO
                location.href = location.pathname + '?' + this.props.pageParam + '=' + page;
            } else {
                this.props.dispatch(setPage(this.props.listId, page, this.props.loadMore));
            }
        }
    }

    _onSelectNext() {
        this._onSelect(this.props.list.page + 1);
    }

}