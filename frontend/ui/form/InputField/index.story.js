import React from 'react';

import {storiesOf} from '@storybook/react';

import InputField from './InputField';

storiesOf('Form', module)
    .add('InputField', () => (
        <InputField label='Text' />
    ));
