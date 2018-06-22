import Model from 'yii-steroids/base/Model';

import {locale} from 'components';

export default class EnumItemEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\EnumItemEntity';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Name'),
                'required': true
            },
            'value': {
                'component': 'InputField',
                'attribute': 'value',
                'label': locale.t('Value')
            },
            'label': {
                'component': 'InputField',
                'attribute': 'label',
                'label': locale.t('Label')
            },
            'cssClass': {
                'component': 'InputField',
                'attribute': 'cssClass',
                'label': locale.t('CSS Class')
            },
            'custom': {
                'component': 'CheckboxField',
                'attribute': 'custom',
                'label': locale.t('Custom values'),
            }
        };
    }

}
