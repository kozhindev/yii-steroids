import React from 'react';

import {storiesOf} from '@storybook/react';

import NumberField from './NumberField';

storiesOf('Form', module)
    .add('NumberField', () => (
        <NumberField label='Amount' />
    ));
