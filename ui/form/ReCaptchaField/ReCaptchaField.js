import React from 'react';
import PropTypes from 'prop-types';

import {resource, ui} from 'components';
import fieldHoc from '../fieldHoc';

export default
@fieldHoc({
    componentId: 'form.ReCaptchaField',
})
class ReCaptchaField extends React.PureComponent {

    static propTypes = {
        metaItem: PropTypes.object,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
    };

    render() {
        const {input, ...props} = this.props;
        const ReCaptchaFieldView = this.props.view || ui.getView('form.ReCaptchaFieldView');
        return (
            <ReCaptchaFieldView
                {...props}
                reCaptchaProps={{
                    sitekey: resource.googleCaptchaSiteKey,
                    onChange: value => input.onChange(value),
                }}
            />
        );
    }

}
