import Model from 'yii-steroids/base/Model';

import {locale} from 'components';

export default class WidgetEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\WidgetEntity';

    static fields() {
        return {
            'moduleId': {
                'component': 'InputField',
                'attribute': 'moduleId',
                'label': locale.t('Module ID'),
                'required': true
            },
            'name': {
                'component': 'InputField',
                'attribute': 'name',
                'label': locale.t('Class name'),
                'required': true
            },
            'parentName': {
                'component': 'InputField',
                'attribute': 'parentName',
                'label': locale.t('Parent widget name'),
                'hint': locale.t('For create child view')
            },
            'withPropTypes': {
                'component': 'CheckboxField',
                'attribute': 'withPropTypes',
                'label': locale.t('With PropTypes')
            },
            'withConnect': {
                'component': 'CheckboxField',
                'attribute': 'withConnect',
                'label': locale.t('With connect()')
            },
            'withGrid': {
                'component': 'CheckboxField',
                'attribute': 'withGrid',
                'label': locale.t('With Grid')
            },
            'withForm': {
                'component': 'CheckboxField',
                'attribute': 'withForm',
                'label': locale.t('With Form')
            },
            'withRouter': {
                'component': 'CheckboxField',
                'attribute': 'withRouter',
                'label': locale.t('With Router')
            }
        };
    }

}
