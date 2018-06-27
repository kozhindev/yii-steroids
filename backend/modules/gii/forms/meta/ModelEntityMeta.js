import Model from 'yii-steroids/base/Model';

import {locale} from 'components';
import MigrateModeMeta from '../../enums/meta/MigrateModeMeta';

export default class ModelEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModelEntity';

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
            'tableName': {
                'component': 'InputField',
                'attribute': 'tableName',
                'label': locale.t('Table name')
            },
            'migrateMode': {
                'component': 'DropDownField',
                'attribute': 'migrateMode',
                'items': MigrateModeMeta,
                'label': locale.t('Migration mode')
            },
            'queryModel': {
                'component': 'InputField',
                'attribute': 'queryModel',
                'label': locale.t('Query model'),
                'hint': locale.t('Set for SearchModel, skip for FormModel')
            },
            'className': {
                'component': 'InputField',
                'attribute': 'className',
                'label': locale.t('Class name')
            }
        };
    }

}
