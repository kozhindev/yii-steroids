import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';

import {ui} from 'components';
import listHoc from '../listHoc';

export default
@listHoc()
class List extends React.PureComponent {

    static propTypes = {
        primaryKey: PropTypes.string,
        items: PropTypes.array,
        itemView: PropTypes.func.isRequired,
        itemProps: PropTypes.object,
        view: PropTypes.func,
    };

    constructor() {
        super(...arguments);

        this.renderItem = this.renderItem.bind(this);
    }

    render() {
        const ListView = this.props.view || ui.getView('list.ListView');
        return (
            <ListView
                {...this.props}
                content={this.props.items && this.props.items.map(this.renderItem)}
            />
        );
    }

    renderItem(item, index) {
        const ItemView = this.props.itemView;
        const id = _get(item, this.props.primaryKey);
        return (
            <ItemView
                {...this.props}
                {...this.props.itemProps}
                key={id || index}
                id={id}
                item={item}
                index={index}
            />
        );
    }

}