import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';

import Grid from './Grid';

storiesOf('Grid', module)
    .add('Grid', context => (
        <div>
            {withInfo()(() => (
                <Grid
                    listId='GridStory'

                />
            ))(context)}
        </div>
    ));