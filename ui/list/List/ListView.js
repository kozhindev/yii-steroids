import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('ListView');

export default class ListView extends React.Component {

    static propTypes = {
        isLoading: PropTypes.bool,
        reverse: PropTypes.bool,
        outsideSearchForm: PropTypes.node,
        paginationSize: PropTypes.node,
        pagination: PropTypes.node,
        content: PropTypes.node,
        empty: PropTypes.node,
    };

    render() {
        if (this.props.reverse) {
            return (
                <div className={bem(bem.block({loading: this.props.isLoading}), this.props.className)}>
                    {this.props.outsideSearchForm}
                    {this.props.paginationSize}
                    {this.props.pagination}
                    {this.props.content}
                    {this.props.empty}
                </div>
            );
        } else {
            return (
                <div className={bem(bem.block(), this.props.className)}>
                    {this.props.outsideSearchForm}
                    {this.props.paginationSize}
                    {this.props.content}
                    {this.props.pagination}
                    {this.props.empty}
                </div>
            );
        }
    }

}
