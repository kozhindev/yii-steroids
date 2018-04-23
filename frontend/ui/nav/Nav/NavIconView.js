import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Button from '../../form/Button';

const bem = html.bem('NavIconView');

export default class NavIconView extends React.Component {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.object),
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                {this.props.items.map((item, index) => (
                    <Button
                        key={index}
                        link
                        onClick={() => this.props.onClick(item, index)}
                        {...item}
                        label={null}
                    />
                ))}
                {this.props.children}
            </div>
        );
    }

}