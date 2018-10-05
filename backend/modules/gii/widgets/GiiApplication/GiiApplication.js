import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Router, Link, Nav} from 'yii-steroids/ui/nav';
import {Notifications} from 'yii-steroids/ui/layout';
import {push} from 'react-router-redux';

import {html, http, widget} from 'components';
import IndexPage from './routes/IndexPage';
import AccessPage from './routes/AccessPage/index';
import ClassCreatorPage from './routes/ClassCreatorPage/index';
import ClassTypeMeta from '../../enums/meta/ClassTypeMeta';

import './GiiApplication.scss';

const bem = html.bem('GiiApplication');

export default
@widget.register('\\steroids\\modules\\gii\\widgets\\GiiApplication\\GiiApplication')
@connect()
class GiiApplication extends React.PureComponent {

    static propTypes = {
        roles: PropTypes.arrayOf(PropTypes.string),
        siteName: PropTypes.string,
    };

    constructor() {
        super(...arguments);

        this._onEntityComplete = this._onEntityComplete.bind(this);

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
            <div className={bem.block({loading: this.state.isLoading})}>
                <nav className='navbar navbar-expand-md navbar-dark bg-dark mb-3'>
                    <div className='container'>
                        <div>
                            <Link
                                className='navbar-brand'
                                to='/'
                            >
                                Gii
                            </Link>
                            <Link
                                className='navbar-brand'
                                url={this.props.siteName}
                            >
                                На главную
                            </Link>
                        </div>
                        <Nav
                            layout='navbar'
                            items={[
                                {
                                    label: 'Сущности',
                                    to: '/',
                                },
                                {
                                    label: 'Права доступа',
                                    to: '/access/actions',
                                },
                                {
                                    label: 'Карта сайта',
                                    to: '/site-map',
                                },
                            ]}
                        />
                    </div>
                </nav>
                <div className={bem(bem.element('content'), 'container')}>
                    <Notifications/>
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
                                    onEntityComplete: this._onEntityComplete,
                                },
                            },
                        ]}
                    />
                </div>
            </div>
        );
    }

    _onEntityComplete() {
        this.props.dispatch(push('/'));
        this.fetchData();
    }

    fetchData() {
        this.setState({isLoading: true});
        http.post('/api/gii/get-entities')
            .then(data => this.setState({
                ...data,
                isLoading: false,
            }));
    }

}
