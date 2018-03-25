import React from 'react';
import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';

import DateTimeField from './DateTimeField';

import './DateTimeFieldView.scss';

storiesOf('Form', module)
    .add('DateTimeField', context => (
        <div>
            {withInfo()(() => (
                <DateTimeField label='Start time' />
            ))(context)}
        </div>
    ));