import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class ModelAttributeEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModelAttributeEntity';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Attribute'),
                'required': true
            },
            'prevName': {
                'component': 'InputField',
                'attribute': 'prevName',
                'label': locale.t('Previous name'),
                'type': 'hidden'
            },
            'label': {
                'component': 'InputField',
                'attribute': 'label',
                'label': locale.t('Label')
            },
            'hint': {
                'component': 'InputField',
                'attribute': 'hint',
                'label': locale.t('Hint')
            },
            'example': {
                'component': 'InputField',
                'attribute': 'example',
                'label': locale.t('Example value')
            },
            'appType': {
                'component': 'InputField',
                'attribute': 'appType',
                'label': locale.t('Type'),
                'required': true
            },
            'defaultValue': {
                'component': 'InputField',
                'attribute': 'defaultValue',
                'label': locale.t('Default value')
            },
            'isRequired': {
                'component': 'CheckboxField',
                'attribute': 'isRequired',
                'label': locale.t('Required'),
            },
            'isPublishToFrontend': {
                'component': 'CheckboxField',
                'attribute': 'isPublishToFrontend',
                'label': locale.t('Publish'),
            }
        };
    }

}
