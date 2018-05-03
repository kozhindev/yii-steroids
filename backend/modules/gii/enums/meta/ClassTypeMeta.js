import Enum from 'yii-steroids/frontend/base/Enum';

import {locale} from 'components';

export default class ClassTypeMeta extends Enum {

    static MODEL = 'model';
    static FORM = 'form';
    static ENUM = 'enum';
    static CRUD = 'crud';
    static WIDGET = 'widget';

    static getLabels() {
        return {
            [this.MODEL]: locale.t('Model ActiveRecord'),
            [this.FORM]: locale.t('Model Form'),
            [this.ENUM]: locale.t('Enum'),
            [this.CRUD]: locale.t('Crud Controller'),
            [this.WIDGET]: locale.t('Widget'),
        };
    }
}
