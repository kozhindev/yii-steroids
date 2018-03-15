import React from 'react';

import {storiesOf} from '@storybook/react';

import Button from './Button';

storiesOf('Form', module)
    .add('Button', () => (
        <Button>
            Hello Button
        </Button>
    ));
