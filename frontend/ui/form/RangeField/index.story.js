import React from 'react';
import PropTypes from 'prop-types';

import { withInfo } from '@storybook/addon-info';
import {text, boolean} from '@storybook/addon-knobs/react';
import {storiesOf} from '@storybook/react';

import RangeField from './RangeField';
import './RangeFieldVIew.scss';

RangeField.propTypes = {
    label: PropTypes.string,
    hint: PropTypes.string,
    attributeFrom: PropTypes.string,
    attributeTo: PropTypes.string,
    inputFrom: PropTypes.shape({
        name: PropTypes.string,
        value: PropTypes.any,
        onChange: PropTypes.func,
    }),
    inputTo: PropTypes.shape({
        name: PropTypes.string,
        value: PropTypes.any,
        onChange: PropTypes.func,
    }),
    required: PropTypes.bool,
    placeholderFrom: PropTypes.string,
    placeholderTo: PropTypes.string,
    disabled: PropTypes.bool,
    inputFromProps: PropTypes.object,
    inputToProps: PropTypes.object,
    onChange: PropTypes.func,
    className: PropTypes.string,
    view: PropTypes.func,
};

RangeField.defaultProps = {
    disabled: false,
};


storiesOf('Form', module)
    .add('RangeField', context => (
        <div>
            {withInfo()(() => (
                <RangeField
                    label={text('Label', 'Range')}
                    disabled={boolean('Disabled', false)}
                    required={boolean('Required', false)}
                    className={text('Class', '')}
                    placeholderFrom={text('Placeholder From')}
                    placeholderTo={text('Placeholder To')}
                />
            ))(context)}
        </div>
    ));
