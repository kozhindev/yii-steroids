import React from 'react';

import {storiesOf} from '@storybook/react';

import DropDownField from './DropDownField';

storiesOf('Form', module)
    .add('DropDownField', () => (
        <div>
            <DropDownField
                label='Single'
                searchPlaceholder='Поиск'
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
            <DropDownField
                label='Multiple'
                searchPlaceholder='Поиск'
                multiple
                autoComplete
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
        </div>
    ));
