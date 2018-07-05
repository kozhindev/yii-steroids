import React from 'react';
import PropTypes from 'prop-types';
import _get from 'lodash-es/get';

import {html, http, locale} from 'components';
import Form from '../../form/Form/Form';

const bem = html.bem('CrudFormView');

@http.hoc(props => {
    const id = _get(props, 'match.params.id');
    return id
        ? http.get(null).then(initialValues => ({initialValues}))
        : Promise.resolve({initialValues: {}});
})
export default class CrudFormView extends React.Component {

    static propTypes = {
        formProps: PropTypes.object,
        initialValues: PropTypes.object,
    };

    render() {
        if (!this.props.initialValues) {
            return null;
        }

        const isNewRecord = !_get(this.props, 'match.params.id');

        return (
            <div className={bem.block()}>
                <Form
                    {...this.props.formProps}
                    initialValues={this.props.initialValues}
                    submitLabel={isNewRecord ? locale.t('Добавить') : locale.t('Сохранить')}
                />
            </div>
        );
    }

}