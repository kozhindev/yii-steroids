import Enum from 'yii-steroids/base/Enum';

export default class ClassTypeMeta extends Enum {

    static MODEL = 'model';
    static FORM = 'form';
    static ENUM = 'enum';
    static CRUD = 'crud';
    static WIDGET = 'widget';

    static getLabels() {
        return {
            [this.MODEL]: __('Model ActiveRecord'),
            [this.FORM]: __('Model Form'),
            [this.ENUM]: __('Enum'),
            [this.CRUD]: __('Crud Controller'),
            [this.WIDGET]: __('Widget'),
        };
    }
}
