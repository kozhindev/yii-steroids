import React from 'react';

import {storiesOf} from '@storybook/react';

import DropDownField from './DropDownField';

storiesOf('Form', module)
    .add('DropDownField', () => (
        <DropDownField
            label='Type'
            items={[
                {
                    id: 1,
                    label: 'First',
                },
                {
                    id: 2,
                    label: 'Second',
                },
            ]}
        />
    ));
