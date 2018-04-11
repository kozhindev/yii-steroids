import React from 'react';
import PropTypes from 'prop-types';
import _isFunction from 'lodash-es/isFunction';
import _isString from 'lodash-es/isString';

import {ui} from 'components';

export default class Format extends React.Component {

    static propTypes = {
        attribute: PropTypes.string,
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
        item: PropTypes.object,
        component: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
    };

    static contextTypes = {
        model: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.func,
        ]),
    };

    static getFormatterPropsFromModel(model, attribute) {
        return attribute && model && _isFunction(model.formatters) && model.formatters()[attribute] || null;
    }

    render() {
        let props = this.props;

        // Get field config from model
        const model = this.props.model || this.context.model;
        props = {
            ...Format.getFormatterPropsFromModel(model, this.props.attribute),
            ...props,
        };

        const ComponentField = _isString(props.component) ? ui.getFormatter('format.' + props.component) : props.component;
        return <ComponentField {...props}/>;
    }

}
