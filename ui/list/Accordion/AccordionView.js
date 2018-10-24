import React from 'react';
import PropTypes from 'prop-types';
import {Collapse} from 'reactstrap';

import {html} from 'components';

const bem = html.bem('AccordionView');

export default class AccordionView extends React.PureComponent {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.oneOfType([
                PropTypes.number,
                PropTypes.string,
                PropTypes.bool,
            ]),
            index: PropTypes.number,
            isOpen: PropTypes.bool,
        })),
        renderHeader: PropTypes.func,
        renderItem: PropTypes.func,
        openedId: PropTypes.oneOfType([
            PropTypes.number,
            PropTypes.string,
        ]),
        onToggle: PropTypes.func,
    };

    render() {
        return (
            <div className={bem.block()}>
                {this.props.items.map(item => (
                    <div
                        key={item.id}
                        className={bem(bem.element('item', {opened: item.isOpened}), 'card mb-3')}
                    >
                        <div
                            className={bem(bem.element('header'), 'card-header')}
                            onClick={() => this.props.onToggle(item)}
                        >
                            {this.props.renderHeader(item)}
                            <span className={bem(bem.element('header-icon'), 'material-icons')}>
                                {item.isOpened ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}
                            </span>
                        </div>
                        <Collapse isOpen={item.isOpened}>
                            <div className={bem(bem.element('content'), 'card-body')}>
                                {this.props.renderItem(item)}
                            </div>
                        </Collapse>
                    </div>
                ))}
            </div>
        );
    }
}
