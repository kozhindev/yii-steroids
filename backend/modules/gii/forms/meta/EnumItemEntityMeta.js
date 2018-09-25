import Model from 'yii-steroids/base/Model';

export default class EnumItemEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\EnumItemEntity';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Name'),
                'required': true
            },
            'value': {
                'component': 'InputField',
                'attribute': 'value',
                'label': __('Value')
            },
            'label': {
                'component': 'InputField',
                'attribute': 'label',
                'label': __('Label')
            },
            'cssClass': {
                'component': 'InputField',
                'attribute': 'cssClass',
                'label': __('CSS Class')
            },
            'custom': {
                'component': 'CheckboxField',
                'attribute': 'custom',
                'label': __('Custom values'),
            }
        };
    }

}
