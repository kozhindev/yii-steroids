import React from 'react';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';

import CheckboxField from './CheckboxField';

storiesOf('Form', module)
    .add('CheckboxField', context => (
        <div>
            {withInfo()(() => (
                <CheckboxField label='Remember me' />
            ))(context)}
        </div>
    ));