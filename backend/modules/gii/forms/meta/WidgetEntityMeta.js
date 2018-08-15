import Model from 'yii-steroids/base/Model';

export default class WidgetEntityMeta extends Model {

    static className = 'steroids\\modules\\gii\\forms\\WidgetEntity';

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
            'parentName': {
                'component': 'InputField',
                'attribute': 'parentName',
                'label': __('Parent widget name'),
                'hint': __('For create child view')
            },
            'withPropTypes': {
                'component': 'CheckboxField',
                'attribute': 'withPropTypes',
                'label': __('With PropTypes')
            },
            'withConnect': {
                'component': 'CheckboxField',
                'attribute': 'withConnect',
                'label': __('With connect()')
            },
            'withGrid': {
                'component': 'CheckboxField',
                'attribute': 'withGrid',
                'label': __('With Grid')
            },
            'withForm': {
                'component': 'CheckboxField',
                'attribute': 'withForm',
                'label': __('With Form')
            },
            'withRouter': {
                'component': 'CheckboxField',
                'attribute': 'withRouter',
                'label': __('With Router')
            }
        };
    }

}
