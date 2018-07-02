import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';

import formIdHoc from '../formIdHoc';
import Field from '../Field';
import {getSecurityFields} from '../../../reducers/fields';

@formIdHoc()
@connect(
    (state, props) => ({
        securityFields: getSecurityFields(state, props.formId),
    })
)
export default class SecurityFields extends React.PureComponent {

    static propTypes = {
        formId: PropTypes.string,
        securityFields: PropTypes.arrayOf(PropTypes.shape({
            label: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.bool,
            ]),
            attribute: PropTypes.string,
            model: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
            ]),
            hint: PropTypes.string,
            required: PropTypes.bool,
            component: PropTypes.oneOfType([
                PropTypes.string,
                PropTypes.func,
            ]),
        })),
    };

    render() {
        return (this.props.securityFields || []).map((field, index) => (
            <Field
                key={index}
                {...field}
            />
        ));
    }

}
