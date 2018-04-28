import React from 'react';
import PropTypes from 'prop-types';
import {Router, Link, Nav} from 'yii-steroids/frontend/ui/nav';

import {html, http, widget} from 'components';
import IndexPage from './routes/IndexPage';
import AccessPage from './routes/AccessPage';
import ClassCreatorPage from './routes/ClassCreatorPage/index';
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
            appTypes: null,
            moduleIds: null,
        };
    }

    componentDidMount() {
        this.fetchData();
    }

    render() {
        return (
            <div className={bem.block()}>
                <nav className='navbar navbar-expand-md navbar-dark bg-dark mb-3'>
                    <div className='container'>
                        <Link
                            className='navbar-brand'
                            to='/'
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
                                    to: '/access',
                                },
                                {
                                    label: 'Карта сайта',
                                    to: '/site-map',
                                },
                            ]}
                        />
                    </div>
                </nav>
                <div className='container'>
                    <Router
                        routes={[
                            {
                                exact: true,
                                path: '/',
                                component: IndexPage,
                                componentProps: {
                                    moduleIds: this.state.moduleIds,
                                    classes: this.state.classes,
                                },
                            },
                            {
                                path: '/access',
                                component: AccessPage,
                            },
                            {
                                path: '/:classType(' + ClassTypeMeta.getKeys().join('|') + ')/:moduleId?/:name?',
                                component: ClassCreatorPage,
                                componentProps: {
                                    moduleIds: this.state.moduleIds,
                                    classes: this.state.classes,
                                    appTypes: this.state.appTypes,
                                },
                            },
                        ]}
                    />
                </div>
            </div>
        );
    }

    fetchData() {
        this.setState({isLoading: true});
        http.post('/api/gii/get-data')
            .then(data => this.setState({
                ...data,
                isLoading: false,
            }));
    }

}
