import React from 'react';
import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import {withReadme} from "storybook-readme";
import {text, boolean, select} from '@storybook/addon-knobs/react';

import TextField from './TextField';
import README from './README.md'


const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('Form', module)
    .addDecorator(withReadme(README))
    .add('TextField', context => (
        <div>
            {withInfo()(() => (
                <TextField
                    label={text('Label', 'Message')}
                    disabled={boolean('Disabled', TextField.defaultProps.disabled)}
                    required={boolean('Required', TextField.defaultProps.required)}
                    className={text('Class', TextField.defaultProps.className)}
                    placeholder={text('Placeholder', PasswordField.defaultProps.placeholder)}
                    size={select('Size', sizes, TextField.defaultProps.size)}
                    submitOnEnter={boolean('SubmitOnEnter', TextField.defaultProps.submitOnEnter)}
                />
            ))(context)}
        </div>
    ));
