import Model from 'yii-steroids/base/Model';

import {locale} from 'components';

export default class ModuleEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\ModuleEntity';

    static fields() {
        return {
            'id': {
                'component': 'InputField',
                'attribute': 'id',
                'label': locale.t('Module ID'),
                'required': true
            },
        };
    }

}
