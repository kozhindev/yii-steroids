import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {Field, FieldArray, reduxForm, formValueSelector, initialize, arrayPush} from 'redux-form';

import {widget} from 'components';
import tableNavigationHandler from '../ModelEditor/tableNavigationHandler';
import EnumMetaArrayField from './views/EnumMetaArrayField';

const FORM_ID = 'EnumEditor';
const selector = formValueSelector(FORM_ID);

@widget.register('\\steroids\\modules\\gii\\widgets\\EnumEditor\\EnumEditor')
@connect(
    state => ({
        formValues: {
            moduleId: selector(state, 'moduleId'),
            enumName: selector(state, 'enumName'),
        }
    })
)
@reduxForm({
    form: FORM_ID,
})
export default class EnumEditor extends React.Component {

    static propTypes = {
        modules: PropTypes.arrayOf(PropTypes.shape({
            id: PropTypes.string,
            className: PropTypes.string,
        })),
        enums: PropTypes.arrayOf(PropTypes.shape({
            className: PropTypes.string,
            name: PropTypes.string,
            module: PropTypes.object,
        })),
        formValues: PropTypes.object,
        csrfToken: PropTypes.string,
    };

    constructor() {
        super(...arguments);

        this._onTableKeyDown = this._onTableKeyDown.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        if ((!this.props.formValues.moduleId || !this.props.formValues.enumName)
            && nextProps.formValues.moduleId && nextProps.formValues.enumName) {

            const enumClass = this.getEnum(nextProps);
            const values = {
                ...nextProps.formValues,
                meta: enumClass ? enumClass.metaClass.meta : [{}],
            };

            this.props.dispatch(initialize(FORM_ID, values));
        }
    }

    getEnum(props) {
        props = props || this.props;
        return props.enums.find(e => e.moduleClass.id === props.formValues.moduleId && e.name === props.formValues.enumName);
    }

    render() {
        return (
            <form
                method='post'
                className='form-horizontal'
            >
                <input
                    type='hidden'
                    name='_csrf'
                    value={this.props.csrfToken}
                />
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Module
                    </label>
                    <div className='col-sm-8'>
                        <Field
                            name='moduleId'
                            component='input'
                            list={`${FORM_ID}_moduleIdList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_moduleIdList`}>
                            {this.props.modules.map(module => (
                                <option key={module.id} value={module.id} />
                            ))}
                        </datalist>
                    </div>
                </div>
                <div className='form-group'>
                    <label className='col-sm-2 control-label'>
                        Enum
                    </label>
                    <div className='col-sm-8'>
                        <Field
                            name='enumName'
                            component='input'
                            list={`${FORM_ID}_enumNameList`}
                            className='form-control'
                        />
                        <datalist id={`${FORM_ID}_enumNameList`}>
                            {this.props.enums.filter(enumClass => enumClass.moduleClass.id === this.props.formValues.moduleId).map(enumClass => (
                                <option key={enumClass.name} value={enumClass.name} />
                            ))}
                        </datalist>
                    </div>
                </div>
                <datalist id={`${FORM_ID}_cssClassList`}>
                    {['success', 'warning', 'danger', 'info'].map(name => (
                        <option key={name} value={name} />
                    ))}
                </datalist>
                {this.props.formValues.moduleId && this.props.formValues.enumName && (
                    <FieldArray
                        name='meta'
                        component={EnumMetaArrayField}
                        onKeyDown={this._onTableKeyDown}
                    />
                )}
                <br />
                {this.props.formValues.moduleId && this.props.formValues.enumName && (
                    <div className='form-group'>
                        <label className='col-sm-2 control-label'>
                            Append inline (optional)
                        </label>
                        <div className='col-sm-8'>
                            <Field
                                name='inlineList'
                                component='textarea'
                                className='form-control'
                            />
                            <p className='hint'>
                                Format: key-value separated by space
                            </p>
                        </div>
                    </div>
                )}
                <div className='form-group'>
                    <div className='col-sm-offset-2 col-sm-6'>
                        <button
                            type='submit'
                            className='btn btn-success'
                        >
                            Сохранить
                        </button>
                    </div>
                </div>
            </form>
        );
    }

    _onTableKeyDown(e) {
        tableNavigationHandler(e, () => this.props.dispatch(arrayPush(FORM_ID, 'meta')));
    }

}