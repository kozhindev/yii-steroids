import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Button from '../../form/Button';
import './NavButtonView.scss';

const bem = html.bem('NavButtonView');

export default class NavButtonView extends React.Component {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.object),
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <div className={bem.element('nav mb-3')}>
                    
                    {this.props.items.map((item, index) => (
                        <Button
                            key={index}
                            color='secondary'
                            outline={!item.isActive}
                            onClick={() => this.props.onClick(item, index)}
                            className={bem.element('nav-item')}
                            {...item}
                        />
                    ))}
                </div>
                <div className={bem.element('content')}>
                    {this.props.children}
                </div>
            </div>
        );
    }
}
