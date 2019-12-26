import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Button from '../../form/Button';

const bem = html.bem('NavListView');

export default class NavListView extends React.Component {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.object),
        className: PropTypes.string,
        navClassName: PropTypes.string,
        onClick: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <div className={bem('list-group', this.props.navClassName, !this.props.children && 'mb-3')}>
                    {this.props.items.map((item, index) => (
                        <Button
                            link
                            onClick={() => this.props.onClick(item, index)}
                            layout={false}
                            {...item}
                            key={index}
                            className={bem(
                                'list-group-item list-group-item-action',
                                item.isActive && 'active',
                                item.className,
                            )}
                        />
                    ))}
                </div>
                {this.props.children}
            </div>
        );
    }

}
