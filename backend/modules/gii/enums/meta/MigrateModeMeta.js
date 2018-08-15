import Enum from 'yii-steroids/base/Enum';

import {locale} from 'components';

export default class MigrateModeMeta extends Enum {

    static UPDATE = 'update';
    static CREATE = 'create';
    static NONE = 'none';

    static getLabels() {
        return {
            [this.UPDATE]: __('Update'),
            [this.CREATE]: __('Create'),
            [this.NONE]: __('None'),
        };
    }
}
