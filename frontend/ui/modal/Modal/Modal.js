import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

import './Modal.scss';

const bem = html.bem('Modal');

export default class Modal extends React.PureComponent {

    static propTypes = {
        title: PropTypes.string,
        className: PropTypes.string,
        onClose: PropTypes.func,
    };

    render() {
        return (
            <div className={bem.block()}>
                <div className={bem(
                    bem.element('header'),
                    'modal-header'
                )}>
                    <h5 className={bem(
                        bem.element('title'),
                        'modal-title'
                    )}>
                        {this.props.title}
                    </h5>
                    <button
                        type="button"
                        className="close"
                        data-dismiss="modal"
                        aria-label="Close"
                        onClick={this.props.onClose}
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div className={bem(bem.element('body'),
                    'modal-body'
                )}>
                    {this.props.children}
                </div>
            </div>
        );
    }

}