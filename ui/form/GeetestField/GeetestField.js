import React from 'react';
import PropTypes from 'prop-types';

import {resource} from 'components';
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
                    product: 'bind',
                    lang: 'en',
                    sandbox: false,
                    offline: !this.props.geetestParams.success,
                }, geetest => {
                    geetest.onSuccess(() => this.props.input.onChange(geetest.getValidate()));
                    setTimeout(() => geetest.verify());
                });
            });
    }

    render() {
        return (
            <div />
        );
    }

}
