import React from 'react';
import {withInfo} from '@storybook/addon-info';
import {storiesOf} from '@storybook/react';
import {withReadme} from "storybook-readme";
import {text, boolean, select} from '@storybook/addon-knobs/react';

import RangeField from './RangeField';
import README from './README.md'

const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('Form', module)
    .addDecorator(withReadme(README))
    .add('RangeField', context => (
        <div>
            {withInfo()(() => (
                <RangeField
                    label={text('Label', 'Range')}
                    disabled={boolean('Disabled', RangeField.defaultProps.disabled)}
                    required={boolean('Required', RangeField.defaultProps.required)}
                    size={select('Size', sizes, RangeField.defaultProps.size)}
                    className={text('Class', RangeField.defaultProps.className)}
                    placeholderFrom={text('Placeholder From', RangeField.defaultProps.placeholderFrom)}
                    placeholderTo={text('Placeholder To', RangeField.defaultProps.placeholderTo)}
                />
            ))(context)}
            <div className='row mb-4'>
                {Object.keys(sizes).map(size => (
                    <div className='col' key={size}>
                        <RangeField label={size} size={size}/>
                    </div>
                ))}
            </div>
            <div className='row'>
                <div className='col'>
                    <RangeField label='Disabled' disabled/>
                </div>
                <div className='col'>
                    <RangeField label='Required' required/>
                </div>
                <div className='col'>
                    <RangeField
                        label='Placeholders'
                        placeholderFrom='From...'
                        placeholderTo='To...'
                    />
                </div>
            </div>
            <RangeField
                type='date'
                label='Date period'
            />
        </div>
    ));
