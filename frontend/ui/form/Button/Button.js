import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';

export default class Button extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
    };

    render() {
        const ButtonView = this.props.buttonView || view.get('form.ButtonView');
        return (
            <ButtonView>
                {this.props.children}
            </ButtonView>
        );
    }

}
