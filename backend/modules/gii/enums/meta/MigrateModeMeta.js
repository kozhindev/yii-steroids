import Enum from 'yii-steroids/frontend/base/Enum';

import {locale} from 'components';

export default class MigrateModeMeta extends Enum {

    static UPDATE = 'update';
    static CREATE = 'create';
    static NONE = 'none';

    static getLabels() {
        return {
            [this.UPDATE]: locale.t('Update'),
            [this.CREATE]: locale.t('Create'),
            [this.NONE]: locale.t('None'),
        };
    }
}
