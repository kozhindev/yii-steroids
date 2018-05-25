import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {formValueSelector} from 'redux-form';
import {Field} from 'yii-steroids/frontend/ui/form';

import {html} from 'components';
import RelationTypeMeta from '../../../../../enums/meta/RelationTypeMeta';

const bem = html.bem('FieldListView');
let relationTypeSelector = null;

@connect(
    (state, props) => ({
        relationType: (relationTypeSelector = relationTypeSelector || formValueSelector(props.formId))(state, props.prefix + 'type'),
    })
)
export default class ModelRelationRow extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        required: PropTypes.bool,
        prefix: PropTypes.string,
        rowIndex: PropTypes.number,
        items: PropTypes.arrayOf(PropTypes.object),
        showRemove: PropTypes.bool,
        onRemove: PropTypes.func,
        renderField: PropTypes.func,
        disabled: PropTypes.bool,
        className: PropTypes.string,
        relationType: PropTypes.string,
    };

    constructor() {
        super(...arguments);

        this.state = {
            relationFocused: false,
            selfFocused: false,
        };
    }

    render() {
        return (
            <tr key={this.props.rowIndex}>
                {this.props.items
                    .filter(field => !field.isVia || this.props.relationType === RelationTypeMeta.MANY_MANY)
                    .map((field, index) => {

                        const inputProps = ['viaRelationKey', 'viaSelfKey'].indexOf(field.attribute) !== -1
                            ? {
                                onFocus: () => this._setFocus(field.attribute === 'viaRelationKey', true),
                                onBlur: () => this._setFocus(field.attribute === 'viaRelationKey', false),
                            }
                            : {};

                        const isHighlighted = field => {
                            const isSelfAttribute = ['selfKey', 'viaSelfKey'].indexOf(field.attribute) !== -1;
                            const isRelatedClassAttribute = [
                                'viaRelationKey', 'relationKey', 'relationModel'
                            ].indexOf(field.attribute) !== -1;

                            return (isSelfAttribute || isRelatedClassAttribute) && this._isFocused(isRelatedClassAttribute);
                        };

                        return (
                            <td
                                key={index}
                                className={bem(
                                    bem.element('table-cell'),
                                    field.className
                                ) + (isHighlighted(field) ? ' bg-success' : '')}
                            >
                                <Field
                                    layout='inline'
                                    {...field}
                                    inputProps={inputProps}
                                    prefix={this.props.prefix}
                                />
                            </td>
                        );
                    })
                }
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
        );
    }


    _isFocused(isRelation) {
        return this.state[isRelation ? 'relationFocuses' : 'selfFocuses'] === true;
    }

    _setFocus(isRelation, isFocus) {
        this.setState({
            [isRelation ? 'relationFocuses' : 'selfFocuses']: isFocus,
        });
    }

}
