import Enum from 'yii-steroids/frontend/base/Enum';

import {locale} from 'components';

export default class ClassTypeMeta extends Enum {

    static MODEL = 'model';
    static FORM = 'form';
    static ENUM = 'enum';

    static getLabels() {
        return {
            [this.MODEL]: locale.t('Модель'),
            [this.FORM]: locale.t('Форма'),
            [this.ENUM]: locale.t('Перечисление'),
        };
    }
}
