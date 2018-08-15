import Enum from 'yii-steroids/base/Enum';

import {locale} from 'components';

export default class RelationTypeMeta extends Enum {

    static HAS_ONE = 'has_one';
    static HAS_MANY = 'has_many';
    static MANY_MANY = 'many_many';

    static getLabels() {
        return {
            [this.HAS_ONE]: __('Has One'),
            [this.HAS_MANY]: __('Has Many'),
            [this.MANY_MANY]: __('Many-Many'),
        };
    }
}
