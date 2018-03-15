import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';

export default class InputField extends React.Component {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        attribute: PropTypes.string,
        value: PropTypes.string,
        onChange: PropTypes.func,
        type: PropTypes.oneOf(['string', 'email', 'hidden', 'phone']),
        placeholder: PropTypes.string,
        disabled: PropTypes.bool,
        inputProps: PropTypes.object,
    };


}
