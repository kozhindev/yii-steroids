import React from 'react';
import PropTypes from 'prop-types';

import {ui} from 'components';

export default class Nav extends React.PureComponent {

    static propTypes = {
        layout: PropTypes.oneOf(['button', 'icon', 'link']),
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.string,
            label: PropTypes.string,
            url: PropTypes.string,
            onClick: PropTypes.func,
            className: PropTypes.string,
            view: PropTypes.func,
        })),
        className: PropTypes.string,
        view: PropTypes.func,
    };

    static defaultProps = {
        layout: 'button',
    };

    render() {
        const defaultViewMap = {
            button: 'NavButtonView',
            icon: 'NavIconView',
            link: 'NavLinkView',
        };
        const NavView = this.props.view || ui.getView(defaultViewMap[this.props.layout]);
        return (
            <NavView
                {...this.props}
            />
        );
    }

}