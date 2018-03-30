import React from 'react';
import PropTypes from 'prop-types';

export default class Field extends React.PureComponent {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        required: PropTypes.bool,
        disabled: PropTypes.bool,
        component: PropTypes.func,
        onChange: PropTypes.func,
        className: PropTypes.string,
        view: PropTypes.func,
    };

    render() {
        const ComponentField = this.props.component;
        return <ComponentField {...this.props}/>;
    }

}
