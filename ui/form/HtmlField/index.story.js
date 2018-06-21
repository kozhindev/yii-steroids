import React from 'react';

import {storiesOf} from '@storybook/react';
import { withInfo } from '@storybook/addon-info';

import HtmlField from './HtmlField';

import './HtmlFieldView.scss';

storiesOf('Form', module)
    .add('HtmlField', context => (
        <div>
            {withInfo()(() => (
                <HtmlField label='Article content' />
            ))(context)}
        </div>
    ));