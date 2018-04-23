import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {select} from '@storybook/addon-knobs/react';

import Nav from './Nav';

const layouts = {
    tabs: 'Tabs',
    button: 'Buttons',
    link: 'Links',
    icon: 'Icons',
};

storiesOf('Nav', module)
    .add('Nav', context => (
        <div>
            {withInfo()(() => (
                <Nav
                    layout={select('Layout', layouts, 'button')}
                    items={[
                        {
                            id: 'general',
                            label: 'Обзор',
                            content: () => (
                                <div>
                                    1
                                </div>
                            ),
                        },
                        {
                            id: 'map',
                            label: 'Маршрут',
                            content: () => (
                                <div>
                                    2
                                </div>
                            ),
                        },
                        {
                            id: 'ship',
                            label: 'Корабль',
                            content: () => (
                                <div>
                                    3
                                </div>
                            ),
                        },
                    ]}
                />
            ))(context)}
        </div>
    ));