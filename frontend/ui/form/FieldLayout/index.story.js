import React from 'react';

import {storiesOf} from '@storybook/react';

import InputField from '../InputField';

storiesOf('Form', module)
    .add('FieldLayout', () => (
        <InputField
            label='Email'
            layout='horizontal'
        />
    ));
