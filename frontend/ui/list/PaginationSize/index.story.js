import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {text, array, select} from '@storybook/addon-knobs/react';

import PaginationSize from './PaginationSize';

const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('List', module)
    .add('PaginationSize', context => (
        <div>
            {withInfo()(() => (
                <PaginationSize
                    //PaginationSize.defaultProps not working
                    sizes={array('Sizes', [30, 50, 100])}
                    className={text('Class', '')}
                    size={select('Size', sizes, 'sm')}
                />
            ))(context)}
        </div>
    ));