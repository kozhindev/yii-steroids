import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

import { ui, clientStorage } from 'components';
import { setLayoutType } from '../../../actions/list';

const LAYOUT_STORAGE_KEY = 'listLayout';

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
            component: PropTypes.oneOf([
                PropTypes.ReactComponent,
                PropTypes.node,
            ]),
        }),
        defaultLayoutItemId: PropTypes.string,
    };

    static defaultProps = {
        layoutItems: [],
    };

    constructor() {
        super(...arguments);

        this._onSelect = this._onSelect.bind(this);

        this.state = {
            layout: clientStorage.get(LAYOUT_STORAGE_KEY) || this.props.defaultLayoutItemId || null,
        };
    }

    componentDidMount() {
        this._onSelect(this.state.layout, true);
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

    _onSelect(layoutId, isInit) {
        if (layoutId && (isInit ? true : layoutId !== this.state.layout)) {
            this.setState(
                {layout: layoutId},
                () => {
                    clientStorage.set(LAYOUT_STORAGE_KEY, layoutId);
                    this.props.dispatch(setLayoutType(this.props.listId, layoutId));
                },
            );
        }
    }
}