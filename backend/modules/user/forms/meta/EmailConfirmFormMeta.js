import Model from 'yii-steroids/base/Model';

export default class EmailConfirmFormMeta extends Model {

    static className = 'steroids\\modules\\user\\forms\\EmailConfirmForm';

    static fields() {
        return {
            'email': {
                'component': 'InputField',
                'attribute': 'email',
                'type': 'email',
                'label': __('Email'),
                'required': true
            },
            'code': {
                'component': 'InputField',
                'attribute': 'code',
                'label': __('Код'),
                'required': true
            }
        };
    }

}
