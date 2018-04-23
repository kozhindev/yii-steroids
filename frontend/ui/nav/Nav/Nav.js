import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';
import _has from 'lodash-es/has';
import _isFunction from 'lodash-es/isFunction';

import {ui} from 'components';

export default class Nav extends React.PureComponent {

    static propTypes = {
        layout: PropTypes.oneOf(['button', 'icon', 'link', 'tabs', 'navbar']),
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.string,
            label: PropTypes.string,
            url: PropTypes.string,
            onClick: PropTypes.func,
            className: PropTypes.string,
            view: PropTypes.func,
            visible: PropTypes.bool,
            content: PropTypes.oneOfType([
                PropTypes.node,
                PropTypes.func,
            ]),
            contentProps: PropTypes.object,
        })),
        activeTab: PropTypes.oneOfType([
            PropTypes.number,
            PropTypes.string,
        ]),
        className: PropTypes.string,
        view: PropTypes.func,
        onChange: PropTypes.func,
    };

    static defaultProps = {
        layout: 'button',
    };

    constructor() {
        super(...arguments);

        this._onClick = this._onClick.bind(this);

        this.state = {
            activeTab: this.props.activeTab || _get(this.props, 'items.0.id') || 0,
        };
    }

    render() {
        const defaultViewMap = {
            button: 'nav.NavButtonView',
            icon: 'nav.NavIconView',
            link: 'nav.NavLinkView',
            tabs: 'nav.NavTabsView',
            navbar: 'nav.NavBarView',
        };
        const NavView = this.props.view || ui.getView(defaultViewMap[this.props.layout]);
        return (
            <NavView
                {...this.props}
                onClick={this._onClick}
                items={this.props.items
                    .map((item, index) => ({
                        ...item,
                        isActive: _has(item, 'id')
                            ? this.state.activeTab === item.id
                            : this.state.activeTab === index,
                    }))
                    .filter(item => item.visible !== false)
                }
            >
                {this.renderContent()}
            </NavView>
        );
    }

    renderContent() {
        const activeItem = this.props.items.find((item, index) => {
            return this.state.activeTab === (_has(item, 'id') ? item.id : index);
        });
        if (!activeItem || !activeItem.content) {
            return null;
        }

        if (_isFunction(activeItem.content)) {
            const ContentComponent = activeItem.content;
            return (
                <ContentComponent
                    {...activeItem}
                    {...activeItem.contentProps}
                />
            );
        }
        return activeItem.content;
    }

    _onClick(item, index) {
        const activeTab = _has(item, 'id') ? item.id : index;
        this.setState({activeTab});

        if (this.props.onChange) {
            this.props.onChange(activeTab);
        }
    }

}