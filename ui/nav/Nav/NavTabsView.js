import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Button from '../../form/Button';

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
                <div className={bem('nav nav-tabs', !this.props.children && 'mb-3')}>
                    {this.props.items.map((item, index) => (
                        <li
                            key={index}
                            className='nav-item'
                        >
                            <Button
                                link
                                onClick={() => this.props.onClick(item, index)}
                                layout={false}
                                {...item}
                                className={bem(
                                    'nav-link',
                                    item.isActive && 'active',
                                    item.className,
                                )}
                            />
                        </li>
                    ))}
                </div>
                {this.props.children}
            </div>
        );
    }

}
