import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Button from '../form/Button';

const bem = html.bem('NavTabsView');

export default class NavTabsView extends React.Component {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.object),
        className: PropTypes.string,
        onClick: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <div className='nav nav-tabs mb-3'>
                    {this.props.items.map((item, index) => (
                        <li
                            key={index}
                            className='nav-item'
                        >
                            <Button
                                link
                                className={bem(
                                    'nav-link',
                                    item.isActive && 'active',
                                )}
                                onClick={() => this.props.onClick(item, index)}
                                {...item}
                            />
                        </li>
                    ))}
                </div>
                {this.props.children}
            </div>
        );
    }

}