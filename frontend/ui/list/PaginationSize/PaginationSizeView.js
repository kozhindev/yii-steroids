import React from 'react';
import PropTypes from 'prop-types';

import {html, locale} from 'components';
import './PaginationSizeView.scss';

const bem = html.bem('PaginationSizeView');

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
                <ul className={bem(bem.element('sizes'), 'pagination pagination-sm')}>
                    {this.props.sizes.map((item, index) => (
                        <li
                            key={index}
                            className={bem(
                                bem.element(
                                    'sizes-item', {
                                        active: item.isActive
                                    }),
                                'page-item',
                                item.isActive && 'active'
                            )}
                        >
                            <a
                                href='javascript:void(0)'
                                className={bem(
                                    bem.element('link'),
                                    'page-link'
                                )}
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
