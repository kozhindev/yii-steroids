import Model from 'yii-steroids/base/Model';

export default class CrudEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\CrudEntity';

    static fields() {
        return {
            'moduleId': {
                'component': 'InputField',
                'attribute': 'moduleId',
                'label': __('Module ID'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Class name'),
                'required': true
            },
            'queryModel': {
                'component': 'InputField',
                'attribute': 'queryModel',
                'label': __('Query model'),
                'required': true
            },
            'searchModel': {
                'component': 'InputField',
                'attribute': 'searchModel',
                'label': __('Search model')
            },
            'title': {
                'component': 'InputField',
                'attribute': 'title',
                'label': __('Title'),
                'required': true
            },
            'url': {
                'component': 'InputField',
                'attribute': 'url',
                'label': __('Url')
            },
            'createActionIndex': {
                'component': 'CheckboxField',
                'attribute': 'createActionIndex',
                'label': __('Index action'),
            },
            'withDelete': {
                'component': 'CheckboxField',
                'attribute': 'withDelete',
                'label': __('With Delete'),
            },
            'withSearch': {
                'component': 'CheckboxField',
                'attribute': 'withSearch',
                'label': __('With Search'),
            },
            'createActionCreate': {
                'component': 'CheckboxField',
                'attribute': 'createActionCreate',
                'label': __('Create action'),
            },
            'createActionUpdate': {
                'component': 'CheckboxField',
                'attribute': 'createActionUpdate',
                'label': __('Update action'),
            },
            'createActionView': {
                'component': 'CheckboxField',
                'attribute': 'createActionView',
                'label': __('View action'),
            }
        };
    }

}
