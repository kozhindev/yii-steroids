import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';

import {html} from 'components';
import {getBreadcrumbs} from '../../../reducers/navigation';
import Link from '../Link';

const bem = html.bem('Breadcrumbs');

export default
@connect(
    (state, props) => ({
        items: props.items || getBreadcrumbs(state, props.pageId, props.params),
    })
)
class Breadcrumbs extends React.PureComponent {

    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.object),
        params: PropTypes.string,
        pageId: PropTypes.string,
        pageTitle: PropTypes.string,
    };

    static defaultProps = {
        items: [],
    };


    render() {
        const items = this.props.items;
        
        return (
            <nav
                className={bem.block()}
                aria-label='breadcrumb'
            >
                <ol className={bem(bem.element('list'), 'breadcrumb')}>
                    {items.map((item, index) => {
                        const isLastItem = items.length === index + 1;

                        return (
                            <li
                                key={item.id}
                                className={bem(bem.element('item'), 'breadcrumb-item')}
                            >
                                {item.url && !isLastItem && (
                                    <Link
                                        to={item.url}
                                    >
                                        {item.title}
                                    </Link>
                                )}

                                {!item.url || isLastItem && (
                                    <span>
                                        {this.props.pageTitle || item.title}
                                    </span>
                                )}
                            </li>
                        );
                    })}
                </ol>
            </nav>
        );
    }
}
