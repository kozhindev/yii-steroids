import React from 'react';
import PropTypes from 'prop-types';
import _merge from 'lodash-es/merge';

import {ui} from 'components';
import fieldHoc from '../fieldHoc';

export default
@fieldHoc({
    componentId: 'form.HtmlField',
})
class HtmlField extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        attribute: PropTypes.string,
        input: PropTypes.shape({
            name: PropTypes.string,
            value: PropTypes.any,
            onChange: PropTypes.func,
        }),
        required: PropTypes.bool,
        disabled: PropTypes.bool,
        editorProps: PropTypes.object,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    static defaultProps = {
        disabled: false,
        className: '',
    };

    static defaultEditorConfig = {
        modules: {
            toolbar: [
                [{'header': [2, 3, false]}],
                ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                [{'list': 'ordered'}, {'list': 'bullet'}, {'indent': '-1'}, {'indent': '+1'}],
                ['link', 'video'],
                ['clean']
            ],
        },
        formats: [
            'header',
            'bold',
            'italic',
            'underline',
            'strike',
            'blockquote',
            'list',
            'bullet',
            'indent',
            'link',
            'video',
            //'image', TODO Image implement
        ],
    };

    render() {
        const HtmlFieldView = this.props.view || ui.getView('form.HtmlFieldView');
        return (
            <HtmlFieldView
                {...this.props}
                editorProps={_merge(
                    HtmlField.defaultEditorConfig,
                    this.props.editorProps,
                    {
                        value: this.props.input.value || '',
                        onChange: value => this.props.input.onChange(value),
                    },
                )}
            />
        );
    }

}
