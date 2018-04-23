import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';

import Nav from './Nav';

storiesOf('Nav', module)
    .add('Nav', context => (
        <div>
            {withInfo()(() => (
                <Nav
                />
            ))(context)}
        </div>
    ));