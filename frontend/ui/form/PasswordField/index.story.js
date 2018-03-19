import React from 'react';

import {storiesOf} from '@storybook/react';

import PasswordField from './PasswordField';

storiesOf('Form', module)
    .add('PasswordField', () => (
        <PasswordField
            label='Password'
            security
        />
    ));
