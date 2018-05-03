import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class ModelRelationEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModelRelationEntity';

    static fields() {
        return {
            'type': {
                'component': 'InputField',
                'attribute': 'type',
                'label': locale.t('Type'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Name'),
                'required': true
            },
            'relationModel': {
                'component': 'InputField',
                'attribute': 'relationModel',
                'label': locale.t('Model class'),
                'required': true
            },
            'relationKey': {
                'component': 'InputField',
                'attribute': 'relationKey',
                'label': locale.t('Relation Key'),
                'required': true
            },
            'selfKey': {
                'component': 'InputField',
                'attribute': 'selfKey',
                'label': locale.t('Self key'),
                'required': true
            },
            'viaTable': {
                'component': 'InputField',
                'attribute': 'viaTable',
                'label': locale.t('Table name')
            },
            'viaRelationKey': {
                'component': 'InputField',
                'attribute': 'viaRelationKey',
                'label': locale.t('Relation Key')
            },
            'viaSelfKey': {
                'component': 'InputField',
                'attribute': 'viaSelfKey',
                'label': locale.t('Self key')
            }
        };
    }

}
