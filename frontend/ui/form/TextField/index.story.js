import React from 'react';
import PropTypes from 'prop-types';
import {storiesOf} from '@storybook/react';

import TextField from './TextField';
import './TextFieldView.scss';
import { withInfo } from '@storybook/addon-info';
import {text, boolean, select} from '@storybook/addon-knobs/react';

TextField.propTypes = {
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
    placeholder: PropTypes.string,
    disabled: PropTypes.bool,
    submitOnEnter: PropTypes.bool,
    inputProps: PropTypes.object,
    onChange: PropTypes.func,
    className: PropTypes.string,
    view: PropTypes.func,
};

TextField.defaultProps = {
    disabled: false,
    size: 'md',
};

const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('Form', module)
    .add('TextField', context => (
        <div>
            {withInfo()(() => (
                <TextField
                    label={text('Label', 'Message')}
                    disabled={boolean('Disabled', false)}
                    required={boolean('Required', false)}
                    size={select('Size', sizes, 'md')}
                    className={text('Class', '')}
                    placeholder={text('Placeholder')}
                    submitOnEnter={boolean('SubmitOnEnter', false)}
                />
            ))(context)}
        </div>
    ));
