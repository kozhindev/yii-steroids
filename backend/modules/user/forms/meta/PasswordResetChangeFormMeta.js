import Model from 'yii-steroids/frontend/base/Model';

export default class PasswordResetChangeFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\PasswordResetChangeForm';

    static fields() {
        return {
            'token': {
                'component': 'InputField',
                'attribute': 'token',
                'required': true
            },
            'newPassword': {
                'component': 'PasswordField',
                'attribute': 'newPassword',
                'required': true
            },
            'newPasswordAgain': {
                'component': 'PasswordField',
                'attribute': 'newPasswordAgain',
                'required': true
            }
        };
    }

}
