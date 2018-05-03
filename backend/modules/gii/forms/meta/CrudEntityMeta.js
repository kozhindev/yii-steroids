import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class CrudEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\CrudEntity';

    static fields() {
        return {
            'moduleId': {
                'component': 'InputField',
                'attribute': 'moduleId',
                'label': locale.t('Module ID'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Class name'),
                'required': true
            },
            'queryModel': {
                'component': 'InputField',
                'attribute': 'queryModel',
                'label': locale.t('Query model'),
                'required': true
            },
            'searchModel': {
                'component': 'InputField',
                'attribute': 'searchModel',
                'label': locale.t('Search model')
            },
            'title': {
                'component': 'InputField',
                'attribute': 'title',
                'label': locale.t('Title'),
                'required': true
            },
            'url': {
                'component': 'InputField',
                'attribute': 'url',
                'label': locale.t('Url')
            },
            'createActionIndex': {
                'component': 'CheckboxField',
                'attribute': 'createActionIndex',
                'label': locale.t('Index action'),
            },
            'withDelete': {
                'component': 'CheckboxField',
                'attribute': 'withDelete',
                'label': locale.t('With Delete'),
            },
            'withSearch': {
                'component': 'CheckboxField',
                'attribute': 'withSearch',
                'label': locale.t('With Search'),
            },
            'createActionCreate': {
                'component': 'CheckboxField',
                'attribute': 'createActionCreate',
                'label': locale.t('Create action'),
            },
            'createActionUpdate': {
                'component': 'CheckboxField',
                'attribute': 'createActionUpdate',
                'label': locale.t('Update action'),
            },
            'createActionView': {
                'component': 'CheckboxField',
                'attribute': 'createActionView',
                'label': locale.t('View action'),
            }
        };
    }

}
