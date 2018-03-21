import React from 'react';
import PropTypes from 'prop-types';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';
import {text, boolean} from '@storybook/addon-knobs/react';

import PasswordField from './PasswordField';
import './PasswordFieldVIew.scss';

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
};

storiesOf('Form', module)
    .add('PasswordField', context => (
        <div>
            {withInfo()(() => (
                <PasswordField
                    label={text('Label', 'Password')}
                    disabled={boolean('Disabled', false)}
                    required={boolean('Required', false)}
                    className={text('Class', '')}
                    placeholder={text('Placeholder')}
                    security={boolean('Security', false)}
                />
            ))(context)}
        </div>
    ));
