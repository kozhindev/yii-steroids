import React from 'react';
import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import { withReadme } from 'storybook-readme';
import {text, boolean} from '@storybook/addon-knobs/react';

import CheckboxField from './CheckboxField';
import README from './README.md';

storiesOf('Form', module)
    .addDecorator(withReadme(README))
    .add('CheckboxField', context => (
        <div>
            {withInfo()(() => (
                <CheckboxField
                    label={text('Label', 'Remember me')}
                    disabled={boolean('Disabled', CheckboxField.defaultProps.disabled)}
                    required={boolean('Required', CheckboxField.defaultProps.required)}
                    className={text('Class', CheckboxField.defaultProps.className)}
                />
            ))(context)}

            <div className='row mb-4'>
                <div className='col-2'>
                    <CheckboxField label='Disabled' disabled/>
                </div>
                <div className='col-2'>
                    <CheckboxField label='Required' required/>
                </div>
            </div>
        </div>
    ));