import React from 'react';
import PropTypes from 'prop-types';
import Gallery from 'react-grid-gallery';

import {locale} from 'components';

import viewHoc from '../viewHoc';

@viewHoc()
export default class EnumFormatter extends React.Component {

    static propTypes = {
        photos: PropTypes.arrayOf(PropTypes.shape({
            uid: PropTypes.number,
            src: PropTypes.string,
            thumbnail: PropTypes.string,
            thumbnailWidth: PropTypes.number,
            thumbnailHeight: PropTypes.number,
        })),
        photoRowHeight: PropTypes.number,
        videoRowHeight: PropTypes.number,
    };

    static defaultProps = {
        photoRowHeight: 120,
    };

    render() {
        return (
            <Gallery
                images={this.props.photos}
                margin={3}
                rowHeight={this.props.photoRowHeight}
                backdropClosesModal={true}
                enableImageSelection={false}
                imageCountSeparator={locale.t(' из ')}
            />
        );
    }

}
