import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class PasswordResetRequestFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\PasswordResetRequestForm';

    static fields() {
        return {
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': locale.t('Email'),
                'required': true
            }
        };
    }

}
