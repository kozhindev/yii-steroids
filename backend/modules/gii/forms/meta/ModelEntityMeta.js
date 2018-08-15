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
                'label': __('Module ID'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Class name'),
                'required': true
            },
            'tableName': {
                'component': 'InputField',
                'attribute': 'tableName',
                'label': __('Table name')
            },
            'migrateMode': {
                'component': 'DropDownField',
                'attribute': 'migrateMode',
                'items': MigrateModeMeta,
                'label': __('Migration mode')
            },
            'queryModel': {
                'component': 'InputField',
                'attribute': 'queryModel',
                'label': __('Query model'),
                'hint': __('Set for SearchModel, skip for FormModel')
            },
            'className': {
                'component': 'InputField',
                'attribute': 'className',
                'label': __('Class name')
            }
        };
    }

}
