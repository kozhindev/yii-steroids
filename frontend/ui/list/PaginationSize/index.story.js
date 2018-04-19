import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {text, array} from '@storybook/addon-knobs/react';

import PaginationSize from './PaginationSize';

storiesOf('List', module)
    .add('PaginationSize', context => (
        <div>
            {withInfo()(() => (
                <PaginationSize
                    //PaginationSize.defaultProps not working
                    sizes={array('Sizes', [30, 50, 100])}
                    className={text('Class', '')}
                />
            ))(context)}
        </div>
    ));