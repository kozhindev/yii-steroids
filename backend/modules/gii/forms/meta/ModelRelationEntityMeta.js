import Model from 'yii-steroids/base/Model';

export default class ModelRelationEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModelRelationEntity';

    static fields() {
        return {
            'type': {
                'component': 'InputField',
                'attribute': 'type',
                'label': __('Type'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Name'),
                'required': true
            },
            'relationModel': {
                'component': 'InputField',
                'attribute': 'relationModel',
                'label': __('Model class'),
                'required': true
            },
            'relationKey': {
                'component': 'InputField',
                'attribute': 'relationKey',
                'label': __('Relation Key')
            },
            'selfKey': {
                'component': 'InputField',
                'attribute': 'selfKey',
                'label': __('Self key')
            },
            'viaTable': {
                'component': 'InputField',
                'attribute': 'viaTable',
                'label': __('Table name')
            },
            'viaRelationKey': {
                'component': 'InputField',
                'attribute': 'viaRelationKey',
                'label': __('Relation Key')
            },
            'viaSelfKey': {
                'component': 'InputField',
                'attribute': 'viaSelfKey',
                'label': __('Self key')
            }
        };
    }

}
