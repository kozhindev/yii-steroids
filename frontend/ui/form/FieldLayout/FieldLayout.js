import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';

export default class FieldLayout extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        required: PropTypes.bool,
        layout: PropTypes.oneOf(['default', 'inline', 'horizontal']),
        layoutProps: PropTypes.object,
        className: PropTypes.string,
        view: PropTypes.func,
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
        const FieldLayoutView = this.props.view || view.get('form.FieldLayoutView');
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
