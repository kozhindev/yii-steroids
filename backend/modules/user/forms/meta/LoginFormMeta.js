import Model from 'yii-steroids/base/Model';

export default class LoginFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\LoginForm';

    static fields() {
        return {
            'login': {
                'component': 'InputField',
                'attribute': 'login',
                'label': __('Логин или email'),
                'required': true
            },
            'password': {
                'component': 'PasswordField',
                'attribute': 'password',
                'label': __('Пароль'),
                'required': true
            },
            'rememberMe': {
                'component': 'CheckboxField',
                'attribute': 'rememberMe',
                'label': __('Запомнить меня')
            },
            'reCaptcha': {
                'component': 'InputField',
                'attribute': 'reCaptcha',
                'label': __('Я не робот')
            },
            'google2faCode': {
                'component': 'InputField',
                'attribute': 'google2faCode',
                'label': __('Google 2FA Code')
            },
        };
    }
}
