import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('ListView');

export default class ListView extends React.Component {

    static propTypes = {
        isLoading: PropTypes.bool,
        reverse: PropTypes.bool,
        searchForm: PropTypes.node,
        paginationSize: PropTypes.node,
        pagination: PropTypes.node,
        content: PropTypes.node,
        empty: PropTypes.node,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                {this.props.searchForm}
                {this.props.paginationSize}
                {this.props.reverse && (
                    <div>
                        {this.props.pagination}
                        {this.props.content}
                    </div>
                ) ||
                (
                    <div>
                        {this.props.content}
                        {this.props.pagination}
                    </div>
                )}
                {this.props.empty}
            </div>
        );
    }

}