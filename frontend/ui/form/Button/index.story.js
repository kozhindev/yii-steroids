import React from 'react';
import PropTypes from 'prop-types';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import { withKnobs, text, boolean, select } from '@storybook/addon-knobs/react';

import Button from './Button';

const storiesButton = storiesOf('Button', module);
storiesButton.addDecorator(withKnobs);

Button.propTypes = {
    label: PropTypes.string,
    type: PropTypes.oneOf(['button', 'submit']),
    size: PropTypes.oneOf(['sm', 'md', 'lg']),
    color: PropTypes.oneOf([
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark',
    ]),
    outline: PropTypes.bool,
    url: PropTypes.string,
    onClick: PropTypes.func,
    disabled: PropTypes.bool,
    submitting: PropTypes.bool,
    block: PropTypes.bool,
    className: PropTypes.string,
    view: PropTypes.func,
};

Button.defaultProps = {
    type: 'button',
    size: 'md',
    color: 'primary',
    outline: false,
    disabled: false,
    submitting: false,
    block: false,
};

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

storiesButton.add('info',
    withInfo(
        {
            inline: true,
        },
    )(() => (
        <Button
            disabled={boolean('Disabled', false)}
            color={select('Color', colors, 'primary')}
            outline={boolean('Outline', false)}
            size={select('Size', sizes, 'md')}
            block={boolean('Block', false)}
            className={text('Class', '')}
            url={text('Link url', '')}
        >
            {text('Label', 'Button')}
        </Button>
    )));

storiesButton.add('examples', () => (
    <div>
        <h2 className='mb-2'>
            Color
        </h2>
        <div className='d-flex mb-3'>
            <div className="mr-3">
                <Button color='primary'>
                    Primary
                </Button>
            </div>
            <div className="mr-3">
                <Button color='secondary'>
                    Secondary
                </Button>
            </div>
            <div className='mr-3'>
                <Button color='success'>
                    Success
                </Button>
            </div>
            <div className="mr-3">
                <Button color='danger'>
                    Danger
                </Button>
            </div>
            <div className="mr-3">
                <Button color='warning'>
                    Warning
                </Button>
            </div>
            <div className="mr-3">
                <Button color='info'>
                    Info
                </Button>
            </div>
            <div className="mr-3">
                <Button color='light'>
                    Light
                </Button>
            </div>
            <div className="mr-3">
                <Button color='dark'>
                    Dark
                </Button>
            </div>
        </div>

        <h2 className='mb-2'>
            Color + Outline
        </h2>
        <div className='d-flex mb-3'>
            <div className="mr-3">
                <Button color='primary' outline>
                    Primary
                </Button>
            </div>
            <div className="mr-3">
                <Button color='secondary' outline>
                    Secondary
                </Button>
            </div>
            <div className='mr-3'>
                <Button color='success' outline>
                    Success
                </Button>
            </div>
            <div className="mr-3">
                <Button color='danger' outline>
                    Danger
                </Button>
            </div>
            <div className="mr-3">
                <Button color='warning' outline>
                    Warning
                </Button>
            </div>
            <div className="mr-3">
                <Button color='info' outline>
                    Info
                </Button>
            </div>
            <div className="mr-3">
                <Button color='light' outline>
                    Light
                </Button>
            </div>
            <div className="mr-3">
                <Button color='dark' outline>
                    Dark
                </Button>
            </div>
        </div>


        <h2 className='mb-2'>
            Size
        </h2>
        <div className='d-flex mb-3'>
            <div className="mr-3">
                <Button size='sm'>
                    Small
                </Button>
            </div>
            <div className="mr-3">
                <Button size='md'>
                    Middle
                </Button>
            </div>
            <div className="mr-3">
                <Button size='lg'>
                    Large
                </Button>
            </div>
        </div>

        <h2 className='mb-2'>
            Disabled
        </h2>
        <div className="d-flex mb-3">
            <div className="mr-3">
                <Button disabled>
                    Disabled
                </Button>
            </div>
        </div>

        <h2 className='mb-2'>
            Link
        </h2>
        <div className="d-flex mb-3">
            <div className="mr-3">
                <Button url='https://google.ru'>
                    Link
                </Button>
            </div>
        </div>

        <h2 className='mb-2'>
            Block
        </h2>
        <div className="row mb-3">
            <div className="col-6">
                <Button block>
                    Block
                </Button>
            </div>
        </div>
    </div>
));


