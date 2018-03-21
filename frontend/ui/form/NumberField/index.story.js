import React from 'react';
import PropTypes from 'prop-types';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import {text, boolean, number, select} from '@storybook/addon-knobs/react';

import NumberField from './NumberField';
import './NumberFieldView.scss';

NumberField.propTypes = {
    label: PropTypes.string,
    hint: PropTypes.string,
    attribute: PropTypes.string,
    input: PropTypes.shape({
        name: PropTypes.string,
        value: PropTypes.any,
        onChange: PropTypes.func,
    }),
    required: PropTypes.bool,
    size: PropTypes.oneOf(['sm', 'md', 'lg']),
    min: PropTypes.number,
    max: PropTypes.number,
    step: PropTypes.number,
    placeholder: PropTypes.string,
    disabled: PropTypes.bool,
    inputProps: PropTypes.object,
    onChange: PropTypes.func,
    className: PropTypes.string,
    view: PropTypes.func,
};

NumberField.defaultProps = {
    disabled: false,
    size: 'md',
};

const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('Form', module)
    .add('NumberField', context => (
        <div>
            {withInfo()(() => (
                <NumberField
                    label={text('Label', 'Amount')}
                    disabled={boolean('Disabled', false)}
                    required={boolean('Required', false)}
                    className={text('Class', '')}
                    placeholder={text('Placeholder')}
                    size={select('Size', sizes, 'md')}
                    step={number('Step')}
                    min={number('min')}
                    max={number('max')}
                />
            ))(context)}
        </div>
    ));