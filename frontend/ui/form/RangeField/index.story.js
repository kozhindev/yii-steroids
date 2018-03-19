import React from 'react';

import {storiesOf} from '@storybook/react';

import RangeField from './RangeField';

storiesOf('Form', module)
    .add('RangeField', () => (
        <RangeField />
    ));
