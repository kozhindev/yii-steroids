import React from 'react';
import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';

import DateField from './DateField';

import './DateFieldView.scss';

storiesOf('Form', module)
    .add('DateField', context => (
        <div>
            {withInfo()(() => (
                <DateField label='Start date' />
            ))(context)}
        </div>
    ));