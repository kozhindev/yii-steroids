import Model from 'yii-steroids/base/Model';

export default class UserMeta extends Model {

    static className = 'steroids\\modules\\user\\models\\User';

    static fields() {
        return {
            'id': {
                'component': 'InputField',
                'attribute': 'id',
                'type': 'hidden',
                'label': __('ИД')
            },
            'login': {
                'component': 'InputField',
                'attribute': 'login',
                'label': __('Логин')
            },
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': __('Email'),
            },
            'phone': {
                'component': 'InputField',
                'attribute': 'phone',
                'type': 'phone',
                'label': __('Телефон')
            },
            'role': {
                'component': 'InputField',
                'attribute': 'role',
                'label': __('Роль')
            },
            'passwordHash': {
                'component': 'TextField',
                'attribute': 'passwordHash',
                'label': __('Пароль')
            },
            'sessionKey': {
                'component': 'InputField',
                'attribute': 'sessionKey',
                'label': __('Ключ сессии')
            },
            'language': {
                'component': 'InputField',
                'attribute': 'language',
                'label': __('Язык')
            },
            'lastLoginIp': {
                'component': 'InputField',
                'attribute': 'lastLoginIp',
                'label': __('IP последнего входа')
            },
            'createTime': {
                'component': 'DateTimeField',
                'attribute': 'createTime',
                'label': __('Дата регистрации')
            },
            'updateTime': {
                'component': 'DateTimeField',
                'attribute': 'updateTime',
                'label': __('Дата обновления')
            },
            'emailConfirmKey': {
                'component': 'InputField',
                'attribute': 'emailConfirmKey',
                'label': __('Ключ подтверждения почты')
            },
            'emailConfirmTime': {
                'component': 'DateTimeField',
                'attribute': 'emailConfirmTime',
                'label': __('Дата подтверждения почты')
            },
            'phoneConfirmKey': {
                'component': 'InputField',
                'attribute': 'phoneConfirmKey',
                'label': __('Ключ подтверждения телефона')
            },
            'phoneConfirmTime': {
                'component': 'DateTimeField',
                'attribute': 'phoneConfirmTime',
                'label': __('Дата подтверждения телефона')
            },
            'blockedTime': {
                'component': 'DateTimeField',
                'attribute': 'blockedTime',
                'label': __('Дата блокировки')
            },
            'lastLoginTime': {
                'component': 'DateTimeField',
                'attribute': 'lastLoginTime',
                'label': __('Дата последнего входа')
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Имя')
            }
        };
    }

    static formatters() {
        return {
            'id': {
                'label': __('ИД')
            },
            'login': {
                'label': __('Логин')
            },
            'email': {
                'label': __('Email')
            },
            'phone': {
                'label': __('Телефон')
            },
            'role': {
                'label': __('Роль')
            },
            'passwordHash': {
                'label': __('Пароль')
            },
            'sessionKey': {
                'label': __('Ключ сессии')
            },
            'language': {
                'label': __('Язык')
            },
            'lastLoginIp': {
                'label': __('IP последнего входа')
            },
            'createTime': {
                'label': __('Дата регистрации')
            },
            'updateTime': {
                'label': __('Дата обновления')
            },
            'emailConfirmKey': {
                'label': __('Ключ подтверждения почты')
            },
            'emailConfirmTime': {
                'label': __('Дата подтверждения почты')
            },
            'phoneConfirmKey': {
                'label': __('Ключ подтверждения телефона')
            },
            'phoneConfirmTime': {
                'label': __('Дата подтверждения телефона')
            },
            'blockedTime': {
                'label': __('Дата блокировки')
            },
            'lastLoginTime': {
                'label': __('Дата последнего входа')
            },
            'name': {
                'label': __('Имя')
            }
        };
    }

}
