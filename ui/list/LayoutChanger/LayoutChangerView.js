import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';

import { html } from 'components';
import Button from '../../form/Button';

const bem = html.bem('LayoutChangerView');

export default class LayoutChangerView extends React.PureComponent {

    static propTypes = {
        className: PropTypes.string,
        items: PropTypes.arrayOf({
            id: PropTypes.string,
            label: PropTypes.string,
            component: PropTypes.node,
            isSelected: PropTypes.bool,
        }),
        onSelect: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(
                bem.block(),
                this.props.className,
                'btn-group',
            )}>
                {this.props.items.map(item => {
                    const LayoutChangerView = item.component || Button;
                    return (
                        <LayoutChangerView
                            key={item.id}
                            {...this.props.buttonProps}
                            className={bem(
                                _get(this.props, 'buttonProps.className'),
                                item.isSelected && 'active',
                            )}
                            disabled={item.isSelected}
                            onClick={() => this.props.onSelect(item)}
                        >
                            {item.label}
                        </LayoutChangerView>
                    );
                })}
            </div>
        );
    }
}