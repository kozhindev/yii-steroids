import React from 'react';
import PropTypes from 'prop-types';

import {html} from 'components';

const bem = html.bem('FileFieldItemView');

export default class FileFieldItemView extends React.PureComponent {

    static propTypes = {
        uid: PropTypes.string,
        fileId: PropTypes.number,
        title: PropTypes.string,
        size: PropTypes.oneOf(['sm', 'md', 'lg']),
        disabled: PropTypes.bool,
        showRemove: PropTypes.bool,
        error: PropTypes.string,
        image: PropTypes.shape({
            url: PropTypes.string,
            width: PropTypes.number,
            height: PropTypes.number,
        }),
        progress: PropTypes.shape({
            uploaded: PropTypes.number,
            percent: PropTypes.number,
        }),
        onRemove: PropTypes.func,

    };

    render() {
        return (
            <div className={bem(bem.block(), 'card float-left mb-1 mr-1')}>
                {this.props.image && (
                    <img
                        src={this.props.image.url}
                        className='card-img-top'
                        width={this.props.image.width}
                        height={this.props.image.height}
                        alt={this.props.title}
                    />
                ) ||
                (
                    <div className={bem.element('blank-img')}>
                        <span className='material-icons'>
                            {this.props.imagesOnly ? 'insert_photo' : 'insert_drive_file'}
                        </span>
                    </div>
                )}
                {this.props.showRemove && (
                    <div
                        className={bem.element('remove')}
                        onClick={this.props.onRemove}
                    >
                        <span className='material-icons'>
                            close
                        </span>
                    </div>
                )}
                <div className='card-body'>
                    <p
                        className={bem(bem.element('text'), 'card-text text-center')}
                        title={this.props.title}
                    >
                        {this.props.title}
                    </p>
                </div>
            </div>
        );
    }

}
