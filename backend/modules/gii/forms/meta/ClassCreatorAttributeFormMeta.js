import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class ClassCreatorAttributeFormMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ClassCreatorAttributeForm';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Атрибут'),
                'required': true
            },
            'label': {
                'component': 'InputField',
                'attribute': 'label',
                'label': locale.t('Название')
            },
            'hint': {
                'component': 'InputField',
                'attribute': 'hint',
                'label': locale.t('Подсказка')
            },
            'example': {
                'component': 'InputField',
                'attribute': 'example',
                'label': locale.t('Пример значения')
            },
            'appType': {
                'component': 'InputField',
                'attribute': 'appType',
                'label': locale.t('Тип'),
                'required': true
            },
            'defaultValue': {
                'component': 'InputField',
                'attribute': 'defaultValue',
                'label': locale.t('Значение по-умолчанию')
            },
            'isRequired': {
                'component': 'CheckboxField',
                'attribute': 'isRequired',
                'label': locale.t('Обязательное поле')
            },
            'isPublishToFrontend': {
                'component': 'CheckboxField',
                'attribute': 'isPublishToFrontend',
                'label': locale.t('Экспортировать по-умолчанию')
            }
        };
    }

}
