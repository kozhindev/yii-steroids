import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';

import PaginationSize from './PaginationSize';

storiesOf('List', module)
    .add('PaginationSize', context => (
        <div>
            {withInfo()(() => (
                <PaginationSize />
            ))(context)}
        </div>
    ));