import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('GeetestFieldView');

import './GeetestFieldView.scss';

export default class GeetestFieldView extends React.PureComponent {

    static propTypes = {
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem.block()}>
            </div>
        );
    }

}
