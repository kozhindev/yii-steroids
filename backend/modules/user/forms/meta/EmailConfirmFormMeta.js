import Model from 'yii-steroids/base/Model';

import {locale} from 'components';

export default class EmailConfirmFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\EmailConfirmForm';

    static fields() {
        return {
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': locale.t('Email'),
                'required': true
            },
            'code': {
                'component': 'InputField',
                'attribute': 'code',
                'label': locale.t('Код'),
                'required': true
            }
        };
    }

}
