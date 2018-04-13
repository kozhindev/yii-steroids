import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';
import Button from '../Button';

const bem = html.bem('FileFieldView');

export default class FileFieldView extends React.PureComponent {

    static propTypes = {
        label: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.bool,
        ]),
        hint: PropTypes.string,
        required: PropTypes.bool,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        buttonComponent: PropTypes.node,
        buttonProps: PropTypes.object,
        itemView: PropTypes.func,
        itemProps: PropTypes.func,
        disabled: PropTypes.bool,
        imagesOnly: PropTypes.bool,
        className: PropTypes.string,
    };

    render() {
        const ButtonComponent = this.props.buttonComponent || Button;
        const FileItemView = this.props.itemView;
        return (
            <div className={bem.block()}>
                <div className={bem(bem.element('files'), 'clearfix')}>
                    {this.props.items.map(item => (
                        <FileItemView
                            key={item.uid}
                            {...item}
                            {...this.props.itemProps}
                        />
                    ))}
                </div>
                <div className={bem.element('button')}>
                    <ButtonComponent
                        {...this.props.buttonProps}
                        label={null}
                    >
                        <span className={bem(
                            bem.element('button-icon'),
                            'material-icons'
                        )}>
                            {this.props.imagesOnly ? 'insert_photo' : 'insert_drive_file'}
                        </span>
                        <span className={bem.element('button-label')}>
                            {this.props.buttonProps.label}
                        </span>
                    </ButtonComponent>
                </div>
            </div>
        );
    }

}
