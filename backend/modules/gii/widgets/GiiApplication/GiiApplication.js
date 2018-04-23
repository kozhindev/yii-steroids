import React from 'react';
import PropTypes from 'prop-types';
import {Router, Link, Nav} from 'yii-steroids/frontend/ui/nav';

import {html, http, widget} from 'components';
import IndexPage from './routes/IndexPage';
import AccessPage from './routes/AccessPage';
import ClassCreatorPage from './routes/ClassCreatorPage';
import ClassTypeMeta from '../../enums/meta/ClassTypeMeta';

import './GiiApplication.scss';

const bem = html.bem('GiiApplication');

@widget.register('\\steroids\\modules\\gii\\widgets\\GiiApplication\\GiiApplication')
export default class GiiApplication extends React.PureComponent {

    static propTypes = {
        roles: PropTypes.arrayOf(PropTypes.string),
    };

    constructor() {
        super(...arguments);

        this.state = {
            isLoading: false,
            classes: null,
        };
    }

    componentDidMount() {
        this.fetchClasses();
    }

    render() {
        return (
            <div className={bem.block()}>
                <nav className='navbar navbar-expand-md navbar-dark bg-dark mb-3'>
                    <Link
                        className='navbar-brand'
                        to='/gii'
                    >
                        Gii
                    </Link>
                    <Nav
                        layout='navbar'
                        items={[
                            {
                                label: 'Сущности',
                                to: '/',
                            },
                            {
                                label: 'Права доступа',
                                to: '/gii/access',
                            },
                            {
                                label: 'Карта сайта',
                                to: '/gii/site-map',
                            },
                        ]}
                    />
                </nav>
                <Router
                    routes={[
                        {
                            exact: true,
                            path: '/gii',
                            component: IndexPage,
                            componentProps: {
                                classes: this.state.classes,
                            },
                        },
                        {
                            path: '/gii/access',
                            component: AccessPage,
                        },
                        {
                            path: '/gii/:classType(' + ClassTypeMeta.getKeys().join('|') + ')/:moduleId?/:name?',
                            component: ClassCreatorPage,
                        },
                    ]}
                />
            </div>
        );
    }

    fetchClasses() {
        this.setState({isLoading: true});
        http.post('/api/gii/fetch-classes')
            .then(classes => this.setState({
                classes,
                isLoading: false,
            }));
    }

}
