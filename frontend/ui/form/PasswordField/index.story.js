import React from 'react';
import PropTypes from 'prop-types';
import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import {withReadme} from "storybook-readme";
import {text, boolean, select} from '@storybook/addon-knobs/react';

import PasswordField from './PasswordField';
import './PasswordFieldVIew.scss';
import README from './README.md'

PasswordField.propTypes = {
    label: PropTypes.string,
    hint: PropTypes.string,
    attribute: PropTypes.string,
    input: PropTypes.shape({
        name: PropTypes.string,
        value: PropTypes.any,
        onChange: PropTypes.func,
    }),
    required: PropTypes.bool,
    size: PropTypes.oneOf(['sm', 'md', 'lg']),
    security: PropTypes.bool,
    placeholder: PropTypes.string,
    disabled: PropTypes.bool,
    inputProps: PropTypes.object,
    onChange: PropTypes.func,
    className: PropTypes.string,
    view: PropTypes.func,
};

PasswordField.defaultProps = {
    disabled: false,
    security: false,
    size: 'md',
};

const sizes = {
    sm: 'Small',
    md: 'Middle',
    lg: 'Large',
};

storiesOf('Form', module)
    .addDecorator(withReadme(README))
    .add('PasswordField', context => (
        <div>
            {withInfo()(() => (
                <PasswordField
                    label={text('Label', 'Password')}
                    disabled={boolean('Disabled', false)}
                    required={boolean('Required', false)}
                    className={text('Class', '')}
                    placeholder={text('Placeholder')}
                    size={select('Size', sizes, 'md')}
                    security={boolean('Security', false)}
                />
            ))(context)}
        </div>
    ));
