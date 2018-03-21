import React from 'react';
import PropTypes from 'prop-types';
import {storiesOf} from '@storybook/react';

import TextField from './TextField';
import './TextFieldView.scss';
import { withInfo } from '@storybook/addon-info';
import {text, boolean} from '@storybook/addon-knobs/react';


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
};

storiesOf('Form', module)
    .add('TextField', context => (
        <div>
            {withInfo()(() => (
                <TextField
                    label={text('Label', 'Message')}
                    disabled={boolean('Disabled', false)}
                    required={boolean('Required', false)}
                    className={text('Class', '')}
                    placeholder={text('Placeholder')}
                    submitOnEnter={boolean('SubmitOnEnter', false)}
                />
            ))(context)}
        </div>
    ));
