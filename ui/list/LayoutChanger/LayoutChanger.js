import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';

import { ui } from 'components';
import { setLayoutType } from '../../../actions/list';

export default
@connect()
class LayoutChanger extends React.PureComponent {

    static propTypes = {
        listId: PropTypes.string,
        className: PropTypes.string,
        view: PropTypes.func,
        layoutItems: PropTypes.arrayOf({
            id: PropTypes.string,
            label: PropTypes.string,
            component: PropTypes.node,
        }),
        defaultLayoutItem: PropTypes.shape({
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
            layout: _get(this.props, 'defaultLayoutItem.id', null),
        };
    }

    componentDidMount() {
        this._onSelect(_get(this.props, 'defaultLayoutItem', null), true);
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

    _onSelect(layout, isInit) {
        if (layout && (isInit ? true : layout.id !== this.state.layout)) {
            this.setState(
                {layout: layout.id},
                () => this.props.dispatch(setLayoutType(this.props.listId, layout.id),
            ));
        }
    }
}