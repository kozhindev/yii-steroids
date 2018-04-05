import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';

import List from './List';

storiesOf('List', module)
    .add('List', context => (
        <div>
            {withInfo()(() => (
                <List
                    listId='ListStory'

                />
            ))(context)}
        </div>
    ));