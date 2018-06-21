import Model from 'yii-steroids/base/Model';

import {locale} from 'components';

export default class UserMeta extends Model {

    static className = 'steroids\\modules\\user\\models\\User';

    static fields() {
        return {
            'id': {
                'component': 'InputField',
                'attribute': 'id',
                'type': 'hidden',
                'label': locale.t('ИД')
            },
            'login': {
                'component': 'InputField',
                'attribute': 'login',
                'label': locale.t('Логин')
            },
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': locale.t('Email'),
                'required': true
            },
            'phone': {
                'component': 'InputField',
                'attribute': 'phone',
                'type': 'phone',
                'label': locale.t('Телефон')
            },
            'role': {
                'component': 'InputField',
                'attribute': 'role',
                'label': locale.t('Роль')
            },
            'passwordHash': {
                'component': 'TextField',
                'attribute': 'passwordHash',
                'label': locale.t('Пароль')
            },
            'sessionKey': {
                'component': 'InputField',
                'attribute': 'sessionKey',
                'label': locale.t('Ключ сессии')
            },
            'language': {
                'component': 'InputField',
                'attribute': 'language',
                'label': locale.t('Язык')
            },
            'lastLoginIp': {
                'component': 'InputField',
                'attribute': 'lastLoginIp',
                'label': locale.t('IP последнего входа')
            },
            'emailConfirmKey': {
                'component': 'InputField',
                'attribute': 'emailConfirmKey',
                'label': locale.t('Ключ подтверждения почты')
            },
            'createTime': {
                'component': 'DateTimeField',
                'attribute': 'createTime',
                'label': locale.t('Дата регистрации')
            },
            'updateTime': {
                'component': 'DateTimeField',
                'attribute': 'updateTime',
                'label': locale.t('Дата обновления')
            },
            'emailConfirmTime': {
                'component': 'DateTimeField',
                'attribute': 'emailConfirmTime',
                'label': locale.t('Дата подтверждения почты')
            },
            'blockedTime': {
                'component': 'DateTimeField',
                'attribute': 'blockedTime',
                'label': locale.t('Дата блокировки')
            },
            'lastLoginTime': {
                'component': 'DateTimeField',
                'attribute': 'lastLoginTime',
                'label': locale.t('Дата последнего входа')
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Имя')
            }
        };
    }

    static formatters() {
        return {
            'id': {
                'label': locale.t('ИД')
            },
            'login': {
                'label': locale.t('Логин')
            },
            'email': {
                'label': locale.t('Email')
            },
            'phone': {
                'label': locale.t('Телефон')
            },
            'role': {
                'label': locale.t('Роль')
            },
            'passwordHash': {
                'label': locale.t('Пароль')
            },
            'sessionKey': {
                'label': locale.t('Ключ сессии')
            },
            'language': {
                'label': locale.t('Язык')
            },
            'lastLoginIp': {
                'label': locale.t('IP последнего входа')
            },
            'emailConfirmKey': {
                'label': locale.t('Ключ подтверждения почты')
            },
            'createTime': {
                'label': locale.t('Дата регистрации')
            },
            'updateTime': {
                'label': locale.t('Дата обновления')
            },
            'emailConfirmTime': {
                'label': locale.t('Дата подтверждения почты')
            },
            'blockedTime': {
                'label': locale.t('Дата блокировки')
            },
            'lastLoginTime': {
                'label': locale.t('Дата последнего входа')
            },
            'name': {
                'label': locale.t('Имя')
            }
        };
    }

}
