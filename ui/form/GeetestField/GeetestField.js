import React from 'react';
import PropTypes from 'prop-types';

import {resource, locale} from 'components';
import fieldHoc from '../fieldHoc';

export default
@fieldHoc({
    componentId: 'form.GeetestField',
})
class GeetestField extends React.PureComponent {

    static propTypes = {
        metaItem: PropTypes.object,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        geetestParams: PropTypes.shape({
            gt: PropTypes.string.isRequired,
            challenge: PropTypes.string.isRequired,
            success: PropTypes.number.isRequired,
        }).isRequired,
    };

    componentDidMount() {
        resource.loadGeetest()
            .then(initGeetest => {
                initGeetest({
                    gt: this.props.geetestParams.gt,
                    challenge: this.props.geetestParams.challenge,
                    https: /https/.test(location.protocol),
                    product: 'float',
                    lang: locale.language,
                    sandbox: false,
                    offline: !this.props.geetestParams.success,
                    //width: '100%',
                }, geetest => {
                    geetest.appendTo(ReactDOM.findDOMNode(this));
                    geetest.onSuccess(() => this._onSuccess(geetest.getValidate()));
                    //geetest.onReady(onReady);
                    //geetest.onRefresh(onRefresh);
                    //geetest.onFail(onFail);
                    //geetest.onError(onError);
                });
            });
    }

    render() {
        const {input, ...props} = this.props;
        const GeetestFieldView = this.props.view || ui.getView('form.GeetestFieldView');
        return (
            <GeetestFieldView
                {...props}
                reCaptchaProps={{
                    sitekey: resource.googleCaptchaSiteKey,
                    onChange: value => input.onChange(value),
                }}
            />
        );
    }

    _onSuccess(result) {
        this.props.input.onChange(result);
    }

}
