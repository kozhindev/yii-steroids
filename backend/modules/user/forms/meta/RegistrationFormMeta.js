import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class RegistrationFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\RegistrationForm';

    static fields() {
        return {
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': locale.t('Email'),
                'required': true
            },
            'password': {
                'component': 'PasswordField',
                'attribute': 'password',
                'label': locale.t('Пароль'),
                'required': true
            },
            'passwordAgain': {
                'component': 'PasswordField',
                'attribute': 'passwordAgain',
                'label': locale.t('Повтор пароля'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Имя')
            }
        };
    }

}
