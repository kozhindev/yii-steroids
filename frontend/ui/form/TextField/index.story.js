import React from 'react';

import {storiesOf} from '@storybook/react';

import TextField from './TextField';

storiesOf('Form', module)
    .add('TextField', () => (
        <TextField label='Message'/>
    ));
