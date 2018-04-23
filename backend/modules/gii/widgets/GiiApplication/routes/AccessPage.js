import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

import './AccessPage.scss';

const bem = html.bem('AccessPage');

export default class AccessPage extends React.PureComponent {

    static propTypes = {
        roles: PropTypes.arrayOf(PropTypes.string),
    };

    render() {
        return (
            <div className={bem.block()}>
                access
            </div>
        );
    }

}
