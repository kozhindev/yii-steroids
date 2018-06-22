import React from 'react';
import PropTypes from 'prop-types';
import {Fade} from 'reactstrap';

import {html} from 'components';

const bem = html.bem('NotificationsItemView');

export default class NotificationsItemView extends React.Component {

    static propTypes = {
        id: PropTypes.number,
        level: PropTypes.string,
        message: PropTypes.string,
        isClosing: PropTypes.bool,
        onClosing: PropTypes.func,
        onClose: PropTypes.func,
    };

    render() {
        return (
            <Fade
                in={!this.props.isClosing}
                onExited={this.props.onClose}
            >
                <div
                    className={bem(
                        bem.block(this.props.level),
                        'alert',
                        'alert-' + this.props.level,
                    )}
                    onClick={this.props.onClosing}
                >
                    <div className={bem.element('message')}>
                        {this.props.message}
                    </div>
                </div>
            </Fade>
        );
    }

}