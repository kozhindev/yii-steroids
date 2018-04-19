import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {text, boolean, select, number} from '@storybook/addon-knobs/react';

import Pagination from './Pagination';

storiesOf('List', module)
    .add('Pagination', context => (
        <div>
            {withInfo()(() => (
                <Pagination
                    aroundCount={number('Around Count', 3)}
                    list={{
                        page: number('Page', 2),
                        pageSize: number('Page Size', 10),
                        total: number('Total', 100),
                    }}
                    loadMore={boolean('Load more', false)}
                />
            ))(context)}
        </div>
    ));