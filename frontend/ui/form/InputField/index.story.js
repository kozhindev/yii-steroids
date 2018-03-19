import React from 'react';
import PropTypes from 'prop-types';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import {text, boolean, select } from '@storybook/addon-knobs/react';

import InputField from './InputField';

InputField.propTypes = {
    label: PropTypes.string,
    hint: PropTypes.string,
    attribute: PropTypes.string,
    input: PropTypes.shape({
        name: PropTypes.string,
        value: PropTypes.any,
        onChange: PropTypes.func,
    }),
    required: PropTypes.bool,
    type: PropTypes.oneOf(['text', 'email', 'hidden', 'phone', 'password']),
    placeholder: PropTypes.string,
    disabled: PropTypes.bool,
    inputProps: PropTypes.object,
    onChange: PropTypes.func,
    className: PropTypes.string,
    view: PropTypes.func,
};

InputField.defaultProps = {
    type: 'text',
    disabled: false,
};


const types = {
    text: 'Text',
    email: 'Email',
    hidden: 'Hidden',
    phone: 'Phone',
    password: 'Password',
}

storiesOf('Form', module)
    .add('InputField', context => (
        <div>
            {withInfo()(() => (
                <InputField
                    label='Text'
                    disabled={boolean('Disabled', false)}
                    className={text('Class', '')}
                    type={select('Type', types, 'text')}
                    placeholder={text('Placeholder')}
                />
            ))(context)}

            <div className="mb-3">
                <InputField label='Text' type='text'/>
            </div>
            <div className="mb-3">
                <InputField label='Email' type='email'/>
            </div>
            <div className="mb-3">
                <InputField label='Phone' type='phone'/>
            </div>
            <div className="mb-3">
                <InputField label='Password' type='password'/>
            </div>
        </div>

    ));