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
                <a
                    href='javascript:void(0)'
                    className={bem.element('close')}
                    onClick={this.props.onClose}
                />
                {this.props.children}
            </div>
        );
    }

}