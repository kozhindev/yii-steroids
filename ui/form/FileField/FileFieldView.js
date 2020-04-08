import React from 'react';
import PropTypes from 'prop-types';
import {SortableContainer, SortableElement} from 'react-sortable-hoc';
import {arraySwap} from 'redux-form';

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
        itemView: PropTypes.elementType,
        itemProps: PropTypes.func,
        disabled: PropTypes.bool,
        imagesOnly: PropTypes.bool,
        className: PropTypes.string,
        sortable: PropTypes.bool,
        onSortChange: PropTypes.func,
    };

    swapFiles = ({oldIndex, newIndex}) => {
        if (oldIndex !== newIndex) {
            this.props.dispatch(arraySwap(this.props.formId, this.props.attribute, oldIndex, newIndex));
        }
    };

    render() {
        const ButtonComponent = this.props.buttonComponent || Button;

        return (
            <div className={bem.block()}>
                {this.renderFilesContainer()}

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

    renderFilesContainer() {
        const FileItemView = this.props.itemView;

        if (!this.props.sortable) {
            return (
                <div className={bem(bem.element('files'), 'clearfix')}>
                    {this.props.items.map(item => (
                        <FileItemView
                            key={item.uid}
                            {...item}
                            {...this.props.itemProps}
                        />
                    ))}
                </div>
            );
        }

        const SortableItem = SortableElement(
            sortableItem => {
                const {fileProps} = sortableItem;

                return (
                    <FileItemView
                        key={fileProps.uid}
                        {...fileProps}
                        {...this.props.itemProps}
                        showOrderIndex={sortableItem.showOrderIndex}
                        orderIndex={sortableItem.orderIndex + 1}
                    />
                );
            }
        );

        const ImagesSortableContainer = SortableContainer(({items}) => {
            return (
                <div className={bem(bem.element('files'), 'clearfix')}>
                    {items.map((item, index) => (
                        <SortableItem
                            key={item.uid}
                            index={index}
                            fileProps={item}
                            showOrderIndex={items.length > 1}
                            // 'index' field is munched by SortableElement HOC, so we duplicating it here
                            orderIndex={index}
                        />
                    ))}
                </div>
            );
        });

        return (
            <ImagesSortableContainer
                axis='xy'
                items={this.props.items}
                distance={1} // enables 'close' button handler
                onSortEnd={this.swapFiles}
            />
        );
    }
}
