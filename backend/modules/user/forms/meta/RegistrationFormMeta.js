import Model from 'yii-steroids/base/Model';

export default class RegistrationFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\RegistrationForm';

    static fields() {
        return {
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': __('Email'),
                'required': true
            },
            'password': {
                'component': 'PasswordField',
                'attribute': 'password',
                'label': __('Пароль'),
                'required': true
            },
            'passwordAgain': {
                'component': 'PasswordField',
                'attribute': 'passwordAgain',
                'label': __('Повтор пароля'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Имя')
            }
        };
    }

}
