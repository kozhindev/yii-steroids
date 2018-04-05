import React from 'react';
import PropTypes from 'prop-types';

import {html, locale} from 'components';

const bem = html.bem('EmptyView');

export default class PaginationSizeView extends React.Component {

    static propTypes = {
        sizes: PropTypes.arrayOf(PropTypes.shape({
            size: PropTypes.number,
            label: PropTypes.node,
            isActive: PropTypes.bool,
        })),
        onSelect: PropTypes.func,
        className: PropTypes.string,
    };

    render() {
        return (
            <div className={bem.block()}>
                <div className={bem.element('label')}>
                    {locale.t('Выводить по')}:
                </div>
                <ul className={bem.element('sizes')}>
                    {this.props.sizes.map((item, index) => (
                        <li
                            key={index}
                            className={bem(
                                bem.element('sizes-item'),
                                bem.element('sizes-item', {active: item.isActive}),
                            )}
                        >
                            <a
                                href='javascript:void(0)'
                                className={bem.element('link')}
                                onClick={() => this.props.onSelect(item.size)}
                            >
                                {item.label}
                            </a>
                        </li>
                    ))}
                </ul>
            </div>
        );
    }

}
