import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {formValueSelector} from 'redux-form';

import {html} from 'components';

const bem = html.bem('FieldListView');
let appTypeSelector = null;

@connect(
    (state, props) => ({
        appType: (appTypeSelector = appTypeSelector || formValueSelector(props.formId))(state, props.prefix + 'appType'),
    })
)
export default class ModelAttributeRow extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        prefix: PropTypes.string,
        rowIndex: PropTypes.number,
        items: PropTypes.arrayOf(PropTypes.object),
        showRemove: PropTypes.bool,
        onRemove: PropTypes.func,
        renderField: PropTypes.func,
        disabled: PropTypes.bool,
        className: PropTypes.string,
        appType: PropTypes.string,
    };

    render() {
        return [
            (
                <tr key='l1'>
                    {this.renderItems(true)}
                    {this.props.showRemove && (
                        <td className={bem.element('table-cell', 'remove')}>
                            {(!this.props.required || this.props.rowIndex > 0) && (
                                <div
                                    className={bem.element('remove')}
                                    onClick={() => this.props.onRemove(this.props.rowIndex)}
                                >
                                    &times;
                                </div>
                            )}
                        </td>
                    )}
                </tr>
            ),
            (
                <tr key='l2'>
                    {this.renderItems(false)}
                    <td
                        colSpan={3}
                        className={bem.element('table-cell')}
                    >
                        <div className='d-flex flex-row w-100'>
                            {this.renderAdditional()}
                        </div>
                    </td>
                </tr>
            )
        ];
    }

    renderItems(isFirstLine) {
        return this.props.items
            .filter(field => isFirstLine === !!field.firstLine)
            .map((field, index) => (
                <td
                    key={index}
                    className={bem(
                        bem.element('table-cell'),
                        field.className
                    )}
                    colSpan={!isFirstLine && index === 0 ? 2 : null}
                >
                    {this.props.renderField(field, this.props.prefix)}
                </td>
            ));
    }

    renderAdditional() {
        const type = (this.props.appTypes || []).find(item => item.name === this.props.appType);
        if (!type || !type.additionalFields) {
            return null;
        }

        return type.additionalFields.map((field, index) => (
            <div
                key={`additional_${index}`}
                className={bem(bem.element('additional'), 'mr-1')}
            >
                {field.component !== 'CheckboxField' && (
                    <div className={bem.element('additional-label')}>
                        {field.label}
                    </div>
                )}
                {this.props.renderField(field, this.props.prefix)}
            </div>
        ));
    }

}
