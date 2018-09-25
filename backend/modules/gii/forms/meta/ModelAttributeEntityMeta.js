import Model from 'yii-steroids/base/Model';

export default class ModelAttributeEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModelAttributeEntity';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Attribute'),
                'required': true
            },
            'prevName': {
                'component': 'InputField',
                'attribute': 'prevName',
                'label': __('Previous name'),
                'type': 'hidden'
            },
            'label': {
                'component': 'InputField',
                'attribute': 'label',
                'label': __('Label')
            },
            'hint': {
                'component': 'InputField',
                'attribute': 'hint',
                'label': __('Hint')
            },
            'example': {
                'component': 'InputField',
                'attribute': 'example',
                'label': __('Example value')
            },
            'appType': {
                'component': 'InputField',
                'attribute': 'appType',
                'label': __('Type'),
                'required': true
            },
            'defaultValue': {
                'component': 'InputField',
                'attribute': 'defaultValue',
                'label': __('Default value')
            },
            'isRequired': {
                'component': 'CheckboxField',
                'attribute': 'isRequired',
                'label': __('Required'),
            },
            'isPublishToFrontend': {
                'component': 'CheckboxField',
                'attribute': 'isPublishToFrontend',
                'label': __('Publish'),
            }
        };
    }

}
