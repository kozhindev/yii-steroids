import React from 'react';
import PropTypes from 'prop-types';
import {SwaggerUIBundle} from 'swagger-ui-dist';

import {html, widget} from 'components';

import './SwaggerUi.scss';

const bem = html.bem('SwaggerUi');
const CONTAINER_ID = 'SwaggerUi'

@widget.register('\\steroids\\modules\\docs\\widgets\\SwaggerUi\\SwaggerUi')
export default class SwaggerUi extends React.PureComponent {

    static propTypes = {
        swaggerUrl: PropTypes.string,
    };

    componentDidMount() {
        SwaggerUIBundle({
            dom_id: '#' + CONTAINER_ID,
            url: this.props.swaggerUrl,
        });
    }

    render() {
        return (
            <div className={bem.block()}>
                <div id={CONTAINER_ID}/>
            </div>
        );
    }

}
