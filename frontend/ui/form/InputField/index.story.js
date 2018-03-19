import React from 'react';
import PropTypes from 'prop-types';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import { withKnobs, text, boolean, select } from '@storybook/addon-knobs/react';

import InputField from './InputField';

const stories = storiesOf('InputField', module);
stories.addDecorator(withKnobs);


stories.add('info',
    withInfo(
        {
            text: 'Some description',
        },
    )(() => (

        <InputField
            input={{
                name: 'test',
            }}
        />
    )));

stories.add('examples', () => (
    <div>
        <InputField
            input={{
                name: 'test',
            }}
        />
    </div>
));


