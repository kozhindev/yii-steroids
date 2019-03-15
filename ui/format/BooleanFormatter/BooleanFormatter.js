import React from 'react';
import PropTypes from 'prop-types';

import viewHoc from '../viewHoc';

export default
@viewHoc()
class BooleanFormatter extends React.Component {

    static propTypes = {
        value: PropTypes.string,
    };

    render() {
        return this.props.value ? __('Да') : __('Нет');
    }

}
