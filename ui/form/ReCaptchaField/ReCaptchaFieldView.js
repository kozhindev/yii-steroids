import React from 'react';
import PropTypes from 'prop-types';
import ReCaptcha from 'react-google-recaptcha';

import {html} from 'components';

const bem = html.bem('ReCaptchaFieldView');

import './ReCaptchaFieldView.scss';

export default class ReCaptchaFieldView extends React.PureComponent {

    static propTypes = {
        className: PropTypes.string,
        reCaptchaProps: PropTypes.object,
    };

    render() {
        return (
            <div className={bem.block()}>
                <ReCaptcha
                    {...this.props.reCaptchaProps}
                    className={bem.element('captcha')}
                />
            </div>
        );
    }

}
