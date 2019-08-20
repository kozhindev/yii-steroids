import React from 'react';
import PropTypes from 'prop-types';
import {Nav, Router} from '../../../../../../../ui/nav';

import {html} from 'components';
import AccessRulesEditor from './views/AccessRulesEditor';

import './AccessPage.scss';

const bem = html.bem('AccessPage');

export default class AccessPage extends React.PureComponent {

    static propTypes = {
        roles: PropTypes.arrayOf(PropTypes.string),
    };

    render() {
        return (
            <div className={bem.block()}>
                <Nav
                    layout='tabs'
                    items={[
                        {
                            label: 'Страницы',
                            to: '/access/actions',
                        },
                        {
                            label: 'Модели',
                            to: '/access/models',
                        },
                    ]}
                />
                <Router
                    routes={[
                        {
                            path: '/access/actions',
                            component: AccessRulesEditor,
                            componentProps: {
                                rulePrefix: 'a',
                            },
                        },
                        {
                            path: '/access/models',
                            component: AccessRulesEditor,
                            componentProps: {
                                rulePrefix: 'm',
                            },
                        },
                    ]}
                />
            </div>
        );
    }

}
