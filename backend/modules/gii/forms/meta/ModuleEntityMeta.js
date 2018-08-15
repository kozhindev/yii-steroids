import Model from 'yii-steroids/base/Model';

export default class ModuleEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModuleEntity';

    static fields() {
        return {
            'id': {
                'component': 'InputField',
                'attribute': 'id',
                'label': __('Module ID'),
                'required': true
            },
        };
    }

}
