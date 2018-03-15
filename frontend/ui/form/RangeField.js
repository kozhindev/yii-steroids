import React from 'react';
import PropTypes from 'prop-types';

import {view} from 'components';

export default class RangeField extends React.Component {

    static propTypes = {
        label: PropTypes.string,
        hint: PropTypes.string,
        attributeFrom: PropTypes.string,
        attributeTo: PropTypes.string,
        value: PropTypes.number,
        onChange: PropTypes.func,
        disabled: PropTypes.bool,
    };


}
