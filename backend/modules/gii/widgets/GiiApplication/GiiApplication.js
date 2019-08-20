import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Router, Link, Nav} from 'yii-steroids/ui/nav';
import {Notifications} from 'yii-steroids/ui/layout';
import {push} from 'react-router-redux';
import _orderBy from 'lodash/orderBy';
import _values from 'lodash/values';

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
            sampleAttributes: [],
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
                                    sampleAttributes: this.state.sampleAttributes,
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

    _getSampleAttributes(classes) {
        const sampleAttributes = {};
        const defaultSamples = {
            id: ['primaryKey', 'ID'],
            title: ['string', 'Название'],
            email: ['email', 'Email'],
            phone: ['phone', 'Телефон'],
            password: ['password', 'Пароль'],
            photo: ['file', 'Фотография'],
            photos: ['files', 'Фотографии'],
            image: ['file', 'Изображение'],
            images: ['files', 'Изображения'],
            file: ['file', 'Файл'],
            files: ['files', 'Файлы'],
            passwordAgain: ['password', 'Повтор пароля'],
            description: ['text', 'Описание'],
            content: ['text', 'Контент'],
            userId: ['integer', 'Пользователь'],
            authorId: ['integer', 'Автор'],
            isEnable: ['boolean', 'Включен?'],
            isDeleted: ['boolean', 'Удален?'],
            status: ['enum', 'Статус'],
            createTime: ['autoTime', 'Добавлен'],
            updateTime: ['autoTime', 'Обновлен', {touchOnUpdate: true}],
        };
        Object.keys(defaultSamples).forEach(id => {
            sampleAttributes[id] = {
                counter: 1,
                params: {
                    appType: defaultSamples[id][0],
                    label: defaultSamples[id][1],
                    ...defaultSamples[id][2],
                }
            };
        });

        [classes.model, classes.form].map(models => {
            models.forEach(model => {
                model.attributeItems.map(item => {
                    if (sampleAttributes[item.name]) {
                        sampleAttributes[item.name].counter++;
                    } else {
                        sampleAttributes[item.name] = {
                            counter: 1,
                            params: {
                                appType: item.appType,
                                defaultValue: item.defaultValue,
                                example: item.example,
                                hint: item.hint,
                                label: item.label,
                            },
                        };
                    }
                });
            });
        });
        Object.keys(sampleAttributes).forEach(id => {
            sampleAttributes[id].id = id;
            sampleAttributes[id].label = id;
        });
        return _orderBy(_values(sampleAttributes), 'counter', 'desc');
    }

    fetchData() {
        this.setState({isLoading: true});
        http.post('/api/gii/get-entities')
            .then(data => this.setState({
                ...data,
                sampleAttributes: this._getSampleAttributes(data.classes),
                isLoading: false,
            }));
    }

}
