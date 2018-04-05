import React from 'react';
import PropTypes from 'prop-types';

import {ui} from 'components';

export default class Empty extends React.PureComponent {

    static propTypes = {
        emptyText: PropTypes.node,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    render() {
        const EmptyView = this.props.view || ui.getView('list.EmptyView');
        return (
            <EmptyView
                {...this.props}
                text={this.props.text}
            />
        );
    }

}