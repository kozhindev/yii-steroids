import React from 'react';
import {storiesOf} from '@storybook/react';

import Form from './Form';
import FieldSet from '../FieldSet';
import TextField from '../TextField';
import InputField from '../InputField';

storiesOf('Form', module)
    .add('Form', () => (
        <Form
            formId='TestForm'
            layout='horizontal'
        >
            <FieldSet prefix='user'>
                <InputField
                    label='Email'
                    attribute='email'
                />
                <TextField
                    label='Message'
                    attribute='message'
                    submitOnEnter
                />
            </FieldSet>
        </Form>
    ));
