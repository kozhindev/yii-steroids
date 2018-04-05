import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';

import Empty from './Empty';

storiesOf('List', module)
    .add('Empty', context => (
        <div>
            {withInfo()(() => (
                <Empty emptyText='Записей не найдено'/>
            ))(context)}
        </div>
    ));