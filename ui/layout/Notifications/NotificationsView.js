import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('NotificationsView');

export default class NotificationsView extends React.Component {

    static propTypes = {
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                {this.props.children}
            </div>
        );
    }

}