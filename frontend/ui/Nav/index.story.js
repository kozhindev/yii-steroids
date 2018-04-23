import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {select, object} from '@storybook/addon-knobs/react';

import Nav from './Nav';

const layouts = {
    tabs: 'Tabs',
    button: 'Buttons',
    link: 'Links',
};

const items = [
    {
        id: 'one',
        label: 'One',
        content: () => (
            <div className='border p-4'>
                <span>One</span>
            </div>
        )
    },
    {
        id: 'two',
        label: 'Two',
        content: () => (
            <div className='border p-4'>
                <span>Two</span>
            </div>
        )
    },
    {
        id: 'three',
        label: 'Three',
        content: () => (
            <div className='border p-4'>
                <span>Three</span>
            </div>
        )
    },

];

storiesOf('Nav', module)
    .add('Nav', context => (
        <div>
            {withInfo()(() => (
                <Nav
                    layout={select('Layout', layouts, 'button')}
                    items={object('Items', items)}
                />
            ))(context)}
        </div>
    ));