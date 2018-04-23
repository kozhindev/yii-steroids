import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class ClassCreatorFormMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ClassCreatorForm';

    static fields() {
        return {
            'moduleId': {
                'component': 'InputField',
                'attribute': 'moduleId',
                'label': locale.t('ИД Модуля')
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Имя класса')
            },
            'tableName': {
                'component': 'InputField',
                'attribute': 'tableName',
                'label': locale.t('Название таблицы в БД')
            },
            'classType': {
                'component': 'InputField',
                'attribute': 'classType',
                'label': locale.t('Тип класса')
            }
        };
    }

}
