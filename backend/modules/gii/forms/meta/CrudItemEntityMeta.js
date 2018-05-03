import Model from 'yii-steroids/frontend/base/Model';

import {locale} from 'components';

export default class CrudItemEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\CrudItemEntity';

    static fields() {
        return {
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Name'),
            },
            'showInForm': {
                'component': 'CheckboxField',
                'attribute': 'showInForm',
                'label': locale.t('Show in Form'),
            },
            'showInTable': {
                'component': 'CheckboxField',
                'attribute': 'showInTable',
                'label': locale.t('Show in Table'),
            },
            'showInView': {
                'component': 'CheckboxField',
                'attribute': 'showInView',
                'label': locale.t('Show in View'),
            }
        };
    }

}
