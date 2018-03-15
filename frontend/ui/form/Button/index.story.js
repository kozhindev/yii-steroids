import React from 'react';

import {storiesOf} from '@storybook/react';

import Button from './Button';

storiesOf('Button', module)
    .add('colors', () => (
        <div className='d-flex'>
            <div className='mr-3'>
                <Button color={'default'}>
                    Default
                </Button>
            </div>
            <div className="mr-3">
                <Button color={'primary'}>
                    Primary
                </Button>
            </div>
            <div className="mr-3">
                <Button color={'info'}>
                    Info
                </Button>
            </div>
            <div className="mr-3">
                <Button color={'success'}>
                    Success
                </Button>
            </div>
            <div className="mr-3">
                <Button color={'warning'}>
                    Warning
                </Button>
            </div>
            <div className="mr-3">
                <Button color={'danger'}>
                    Danger
                </Button>
            </div>
        </div>
    ))
    .add('sizes', () => (
        <div className='d-flex'>
            <div className='mr-3'>
                <Button size='sm'>
                    Size xs
                </Button>
            </div>
            <div className="mr-3">
                <Button size='md'>
                    Size md
                </Button>
            </div>
            <div className="mr-3">
                <Button size='lg'>
                    Size lg
                </Button>
            </div>
        </div>
    ))
    .add('states', () => (
        <div>
            <div className='row mb-3'>
                <div className="col-12 d-flex">
                    <div className='mr-3'>
                        <Button
                            color='primary'
                            disabled
                        >
                            Disabled
                        </Button>
                    </div>
                    <div className="mr-3">
                        <Button
                            color='primary'
                            submitting
                        >
                            Submitting
                        </Button>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-6">
                    <div className='mr-3'>
                        <Button
                            block
                        >
                            Block
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    ));

