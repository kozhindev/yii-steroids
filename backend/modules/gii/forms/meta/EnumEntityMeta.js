import Model from 'yii-steroids/base/Model';

import {locale} from 'components';

export default class EnumEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\EnumEntity';

    static fields() {
        return {
            'moduleId': {
                'component': 'InputField',
                'attribute': 'moduleId',
                'label': __('Module ID'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': __('Class name'),
                'required': true
            },
            'isCustomValues': {
                'component': 'CheckboxField',
                'attribute': 'isCustomValues',
                'label': __('Use custom values'),
            }
        };
    }

}
