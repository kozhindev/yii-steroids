import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('EmptyView');

export default class EmptyView extends React.Component {

    static propTypes = {
        text: PropTypes.string,
    };

    render() {
        return (
            <div className={bem.block()}>
                {this.props.text}
            </div>
        );
    }

}