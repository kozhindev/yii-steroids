import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('DateTimeFieldView');
import './DateTimeFieldView.scss';

export default class DateTimeFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        dateField: PropTypes.node,
        timeField: PropTypes.node,
        className: PropTypes.string,
        isInvalid: PropTypes.bool,
    };

    render() {
        return (
            <div className={bem(bem.block())}>
                <div className={bem.element('date')}>
                    {this.props.dateField}
                </div>
                <div className={bem.element('time')}>
                    {this.props.timeField}
                </div>
            </div>
        );
    }

}
