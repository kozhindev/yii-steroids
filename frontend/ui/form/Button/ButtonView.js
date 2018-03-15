import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('ButtonView');

export default class ButtonView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
    };

    render() {
        return (
            <button className={bem.block()}>
                {this.props.children}
            </button>
        );
    }

}
