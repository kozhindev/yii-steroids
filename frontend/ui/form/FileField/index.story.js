import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {withReadme} from "storybook-readme";

import FileField from './FileField';

import './FileFieldView.scss';

storiesOf('Form', module)
    .add('FileField', context => (
        <div>
            {withInfo()(() => (
                <FileField label='File'/>
            ))(context)}
        </div>
    ));