import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('ModalView');

export default class ModalView extends React.PureComponent {

    static propTypes = {
        onClose: PropTypes.func,
        children: PropTypes.node,
    };

    render() {
        return (
            <div className={bem.block()}>
                {this.props.children}
            </div>
        );
    }

}