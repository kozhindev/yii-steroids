import React from 'react';
import PropTypes from 'prop-types';
import {Collapse} from 'reactstrap';
import {connect} from 'react-redux';
import {Field, formValueSelector} from 'redux-form';

import {html} from 'components';
import PermissionCheckbox from './PermissionCheckbox';

import './PermissionRow.scss';

const bem = html.bem('PermissionRow');
const FORM_ID = 'AccessRulesEditor';
const selector = formValueSelector(FORM_ID);

@connect(
    (state, props) => {
        // Count child checked items
        let checkedCount = 0;
        const formRules = selector(state, 'rules') || {};
        const countChecked = function(permissionName) {
            Object.keys(formRules).map(role => {
                if (formRules[role][permissionName]) {
                    checkedCount++;
                }
            });

            const permission = props.permissions.find(permission => permission.name === permissionName);
            (permission.children || []).forEach(countChecked);
        };
        countChecked(props.permission.name);

        return {
            checkedCount,
        };
    }
)
export default class PermissionRow extends React.PureComponent {

    static propTypes = {
        roles: PropTypes.arrayOf(PropTypes.string),
        permissions: PropTypes.arrayOf(PropTypes.shape({
            name: PropTypes.string,
            description: PropTypes.string,
            children: PropTypes.arrayOf(PropTypes.string),
        })),
        permission: PropTypes.shape({
            name: PropTypes.string,
            description: PropTypes.string,
            children: PropTypes.arrayOf(PropTypes.string),
        }),
        parentPermission: PropTypes.string,
        level: PropTypes.number,
        checkedCount: PropTypes.number,
        enableInlineMode: PropTypes.bool,
        visible: PropTypes.bool,
    };

    static defaultProps = {
        level: 0,
    };

    constructor() {
        super(...arguments);

        this.state = {
            isExpanded: false,
        };
    }

    render() {
        const WrappedPermissionRow = exports.default;

        if (!this.props.visible) {
            return (
                <div>
                    {this.props.roles.map(role => (
                        <Field
                            key={role}
                            name={`rules[${role}][${this.props.permission.name}]`}
                            component='input'
                            type='hidden'
                        />
                    ))}
                    {this.getChildren().map(children => (
                        <WrappedPermissionRow
                            key={children.name}
                            permission={children}
                            roles={this.props.roles}
                            permissions={this.props.permissions}
                            enableInlineMode={this.props.enableInlineMode}
                            level={this.props.level + 1}
                            visible={false}
                        />
                    ))}
                </div>
            );
        }

        return (
            <div className={bem.block()}>
                <div
                    style={{
                        width: (400 + (this.props.roles.length * 200)),
                    }}
                >
                    <div
                        className={bem.element('container')}
                        style={{width: 400}}
                    >
                        {this.getChildren().length > 0 && (
                            <a
                                href='javascript:void(0)'
                                className={bem.element('link')}
                                onClick={() => this.setState({isExpanded: !this.state.isExpanded})}
                                style={{
                                    marginLeft: 30 * this.props.level,
                                }}
                            >
                                <span className={bem.element('collapse-icon')}>
                                    {!this.state.isExpanded ? '+' : '-'}
                                </span>
                                <span className={bem.element('description')}>
                                    <code>
                                        {this.props.permission.description}
                                    </code>
                                    &nbsp;
                                    {this.props.checkedCount > 0 && (
                                        <span className='badge'>
                                            {this.props.checkedCount}
                                        </span>
                                    )}
                                </span>
                            </a>
                        ) ||
                        (
                            <div
                                className={bem.element('link')}
                                style={{
                                    marginLeft: 30 * this.props.level,
                                }}
                            >
                                <span className={bem.element('description')}>
                                    <code>
                                        {this.props.permission.description}
                                    </code>
                                </span>
                            </div>
                        )}
                    </div>
                    {this.props.roles.map(role => (
                        <div
                            key={role}
                            style={{width: 200}}
                            className={bem.element('checkboxes')}
                        >
                            <PermissionCheckbox
                                permissions={this.props.permissions}
                                permission={this.props.permission}
                                role={role}
                            />
                            {this.getInline().map(children => (
                                <PermissionCheckbox
                                    key={children.name}
                                    permissions={this.props.permissions}
                                    permission={children}
                                    role={role}
                                    showTooltip
                                />
                            ))}
                        </div>
                    ))}
                </div>
                {this.getChildren().length > 0 && (
                    <Collapse isOpen={this.state.isExpanded}>
                        <div>
                            {this.getChildren().map(children => (
                                <WrappedPermissionRow
                                    key={children.name}
                                    permission={children}
                                    roles={this.props.roles}
                                    permissions={this.props.permissions}
                                    enableInlineMode={this.props.enableInlineMode}
                                    level={this.props.level + 1}
                                    visible={this.state.isExpanded}
                                />
                            ))}
                        </div>
                    </Collapse>
                )}
            </div>
        );
    }

    getChildren() {
        return this._getItems(false);
    }

    getInline() {
        if (!this.props.enableInlineMode) {
            return [];
        }
        return this._getItems(true);
    }

    _getItems(isInline) {
        if (!this.props.permission.children) {
            return [];
        }
        return this.props.permissions.filter(permission => {
            if (this.props.permission.children.indexOf(permission.name) === -1) {
                return false;
            }

            if (this.props.enableInlineMode) {
                return isInline === !permission.children;
            }
            return true;
        });
    }

}
