import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import { withReadme } from 'storybook-readme';
import {text, boolean, select} from '@storybook/addon-knobs/react';

import Button from './Button';
import README from './README.md';

import {defaultProps} from './Button';

const colors = {
    primary: 'Primary',
    secondary: 'Secondary',
    success: 'Success',
    danger: 'Danger',
    warning: 'Warning',
    info: 'Info',
    light: 'Light',
    dark: 'Dark',
};
const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('Form', module)
    .addDecorator(withReadme(README))
    .add('Button', context => (
        <div>
            {withInfo()(() => (
                <Button
                    disabled={boolean('Disabled', defaultProps.disabled)}
                    color={select('Color', colors, defaultProps.color)}
                    outline={boolean('Outline', defaultProps.outline)}
                    size={select('Size', sizes, defaultProps.size)}
                    block={boolean('Block', defaultProps.block)}
                    className={text('Class', defaultProps.className)}
                    url={text('Link url', defaultProps.url)}
                >
                    {text('Label', 'Button')}
                </Button>
            ))(context)}
            <h6>
                Colors
            </h6>
            <div className='clearfix mb-2'>
                {Object.keys(colors).map(color => (
                    <Button
                        key={color}
                        color={color}
                        className='float-left mr-2'
                    >
                        {colors[color]}
                    </Button>
                ))}
            </div>
            <div className='clearfix mb-4'>
                {Object.keys(colors).map(color => (
                    <Button
                        key={color}
                        color={color}
                        className='float-left mr-2'
                        outline
                    >
                        {colors[color]}
                    </Button>
                ))}
            </div>
            <h6>
                Size
            </h6>
            <div className='clearfix mb-4'>
                {Object.keys(sizes).map(size => (
                    <Button
                        key={size}
                        size={size}
                        className='float-left mr-2'
                    >
                        {sizes[size]}
                    </Button>
                ))}
            </div>
            <div className='clearfix mb-4'>
                <div className='float-left mr-4'>
                    <h6>
                        Disabled
                    </h6>
                    <Button disabled>
                        Disabled
                    </Button>
                </div>
                <div className='float-left mr-4'>
                    <h6>
                        Disabled
                    </h6>
                    <Button disabled>
                        Disabled
                    </Button>
                </div>
                <div className='float-left mr-4 col-2'>
                    <h6>
                        Block
                    </h6>
                    <Button block>
                        Block
                    </Button>
                </div>
                <div className='float-left mr-4'>
                    <h6>
                        Link
                    </h6>
                    <Button url='https://google.ru'>
                        Link
                    </Button>
                </div>
            </div>
        </div>
));
