import React from 'react';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';

import RadioListField from './RadioListField';

storiesOf('Form', module)
    .add('RadioListField', context => (
        <div>
            {withInfo()(() => (
                <RadioListField
                    label='Choose type'
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
            ))(context)}
        </div>
    ));