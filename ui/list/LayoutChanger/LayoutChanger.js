import React from 'react';
import PropTypes from 'prop-types';

import { ui } from 'components';

export default class LayoutChanger extends React.PureComponent {

    static propTypes = {
        listId: PropTypes.string,
        className: PropTypes.string,
        view: PropTypes.func,
        layoutItems: PropTypes.arrayOf({
            id: PropTypes.string,
            label: PropTypes.string,
            component: PropTypes.node,
        }),
    };

    static defaultProps = {
        layoutItems: [],
    };

    constructor() {
        super(...arguments);

        this._onSelect = this._onSelect.bind(this);

        this.state = {
            layout: null,
        };
    }

    render() {
        const LayoutChangerView = this.props.view || ui.getView('list.LayoutChangerView');
        return (
            <LayoutChangerView
                {...this.props}
                items={this.props.layoutItems.map(item => ({
                    ...item,
                    isSelected: this.state.layout === item.id,
                }))}
                layout={this.state.layout}
                onSelect={this._onSelect}
            />
        );
    }

    _onSelect(layout) {
        if (layout && layout.id !== this.state.layout) {
            this.setState({layout: layout.id});
        }
    }
}