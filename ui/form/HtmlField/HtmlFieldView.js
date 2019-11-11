import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('HtmlFieldView');

let ReactQuill = null;
if (!process.env.IS_SSR) {
    ReactQuill = require('react-quill').default;
    const ImageUpload = require('quill-image-uploader').default;
    ReactQuill.Quill.register('modules/imageUploader', ImageUpload);
}

export default class HtmlFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        disabled: PropTypes.bool,
        editorProps: PropTypes.object,
        className: PropTypes.string,
    };

    render() {
        if (process.env.IS_SSR) {
            return null;
        }

        return (
            <div className={bem.block()}>
                <ReactQuill {...this.props.editorProps} />
            </div>
        );
    }

}
