import React from 'react';
import PropTypes from 'prop-types';

import {locale} from 'components';

export default class FileSize extends React.Component {

    static propTypes = {
        bytes: PropTypes.number,
        showZero: PropTypes.bool,
    };

    static asHumanFileSize(bytes, showZero) {
        if (!bytes) {
            return showZero ? '0' : '';
        }

        const thresh = 1000;
        if (Math.abs(bytes) < thresh) {
            return bytes + ' ' + locale.t('B');
        }
        const units = [
            locale.t('kB'),
            locale.t('MB'),
            locale.t('GB'),
            locale.t('TB'),
            locale.t('PB'),
            locale.t('EB'),
            locale.t('ZB'),
            locale.t('YB'),
        ];
        let u = -1;
        do {
            bytes /= thresh;
            ++u;
        } while (Math.abs(bytes) >= thresh && u < units.length - 1);
        return bytes.toFixed(1) + ' ' + units[u];
    }

    render() {
        return FileSize.asHumanFileSize(this.props.bytes, this.props.showZero);
    }

}
