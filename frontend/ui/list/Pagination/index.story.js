import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';

import Pagination from './Pagination';

storiesOf('List', module)
    .add('Pagination', context => (
        <div>
            {withInfo()(() => (
                <Pagination />
            ))(context)}
        </div>
    ));