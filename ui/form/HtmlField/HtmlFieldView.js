import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
const bem = html.bem('HtmlFieldView');

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
        // TODO Component quill is breaked on SSR when import
        const ReactQuill = process.env.IS_NODE ? () => null : require('react-quill').default;
        return (
            <div className={bem.block()}>
                <ReactQuill {...this.props.editorProps} />
            </div>
        );
    }

}
