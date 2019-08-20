import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';

import {html} from 'components';
import Button from '../Button';

const bem = html.bem('SwitcherFieldView');

export default class SwitcherFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        className: PropTypes.string,
        buttonProps: PropTypes.object,
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
                PropTypes.bool,
            ]),
            label: PropTypes.string,
        })),
        onItemClick: PropTypes.func,
    };

    render() {
        return (
            <div className={bem(
                bem.block({
                    size: this.props.size,
                }),
                this.props.className,
                'btn-group',
            )}>
                {this.props.items.map(item => (
                    <Button
                        key={item.id}
                        {...this.props.buttonProps}
                        className={bem(
                            _get(this.props, 'buttonProps.className'),
                            item.isSelected && 'active',
                        )}
                        disabled={this.props.disabled}
                        onClick={() => this.props.onItemClick(item)}
                        layout={false}
                    >
                        {item.label}
                    </Button>
                ))}
            </div>
        );
    }

}
