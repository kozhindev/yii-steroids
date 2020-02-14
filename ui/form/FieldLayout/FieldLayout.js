import React from 'react';
import PropTypes from 'prop-types';

import {ui} from 'components';

export default class FieldLayout extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
            PropTypes.element,
        ]),
        hint: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        required: PropTypes.bool,
        layout: PropTypes.oneOfType([
            PropTypes.oneOf(['default', 'inline', 'horizontal']),
            PropTypes.string,
            PropTypes.bool,
        ]),
        layoutProps: PropTypes.object,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        errors: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.arrayOf(PropTypes.string),
        ]),
        layoutClassName: PropTypes.string,
        layoutView: PropTypes.elementType,
    };

    static defaultProps = {
        layout: 'default',
        layoutProps: {
            cols: [3, 6],
        },
        required: false,
        className: '',
    };

    render() {
        if (this.props.layout === false) {
            return this.props.children;
        }

        const FieldLayoutView = this.props.layoutView || ui.getView('form.FieldLayoutView');
        return (
            <FieldLayoutView
                {...this.props}
                layoutProps={{
                    ...FieldLayout.defaultProps.layoutProps,
                    ...this.props.layoutProps,
                }}
            >
                {this.props.children}
            </FieldLayoutView>
        );
    }

}
