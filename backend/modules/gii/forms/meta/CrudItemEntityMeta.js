import Model from 'yii-steroids/base/Model';

export default class CrudItemEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\CrudItemEntity';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Name'),
            },
            'showInForm': {
                'component': 'CheckboxField',
                'attribute': 'showInForm',
                'label': __('Show in Form'),
            },
            'showInTable': {
                'component': 'CheckboxField',
                'attribute': 'showInTable',
                'label': __('Show in Table'),
            },
            'showInView': {
                'component': 'CheckboxField',
                'attribute': 'showInView',
                'label': __('Show in View'),
            }
        };
    }

}
