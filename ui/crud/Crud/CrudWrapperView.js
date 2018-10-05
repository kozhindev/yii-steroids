import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Nav from '../../nav/Nav';

const bem = html.bem('CrudWrapperView');

export default class CrudWrapperView extends React.Component {

    static propTypes = {
        controls: PropTypes.array,
        navProps: PropTypes.object,
    };

    render() {
        return (
            <div className={bem(bem.block(), this.props.className)}>
                <div className={bem(bem.element('controls'), 'd-flex justify-content-between')}>
                    {this.renderControls(this.props.controls.filter(item => item.position !== 'right'))}
                    {this.renderControls(this.props.controls.filter(item => item.position === 'right'))}
                </div>

                {this.props.children}
            </div>
        );
    }

    renderControls(controls) {
        if (controls.length === 0) {
            return null;
        }
        return (
            <Nav
                layout='button'
                {...this.props.navProps}
                items={controls}
            />
        );
    }

}
