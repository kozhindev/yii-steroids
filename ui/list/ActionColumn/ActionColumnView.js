import React from 'react';
import PropTypes from 'prop-types';
import Nav from '../../nav/Nav';

import {html} from 'components';

const bem = html.bem('ActionColumnView');

export default class ActionColumnView extends React.PureComponent {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
                PropTypes.bool,
            ]),
            icon: PropTypes.string,
            label: PropTypes.string,
        })),
    };

    render() {
        return (
            <div className={bem.block()}>
                <Nav
                    {...this.props}
                    layout='icon'
                    items={this.props.items}
                />
            </div>
        );
    }
}