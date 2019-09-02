import React from 'react';
import PropTypes from 'prop-types';

import { ui } from 'components';

const LAYOUT_DEFAULT = 'layout_default';
const LAYOUT_ONE = 'layout_one';
const LAYOUT_TWO = 'layout_two';
const LAYOUT_THREE = 'layout_three';

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
        layoutItems: [
            {
                id: LAYOUT_DEFAULT,
                label: LAYOUT_DEFAULT,
                component: null,
            },
            {
                id: LAYOUT_ONE,
                label: LAYOUT_ONE,
                component: null,
            },
            {
                id: LAYOUT_TWO,
                label: LAYOUT_TWO,
                component: null,
            },
            {
                id: LAYOUT_THREE,
                label: LAYOUT_THREE,
                component: null,
            },
        ],
    };

    constructor() {
        super(...arguments);

        this._onSelect = this._onSelect.bind(this);

        this.state = {
            layout: LAYOUT_DEFAULT,
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