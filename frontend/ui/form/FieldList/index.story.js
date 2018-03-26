import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {withReadme} from "storybook-readme";
import {text} from '@storybook/addon-knobs/react';

import FieldList from './FieldList';
import Form from '../Form';
import InputField from '../InputField';
import NumberField from '../NumberField';
import README from './README.md'

import './FieldListView.scss';

storiesOf('Form', module)
    .addDecorator(withReadme(README))
    .add('FieldList', context => (
        <div>
            {withInfo()(() => (
                <Form
                    formId='FieldListForm'
                    layout='horizontal'
                >
                    <FieldList
                        attribute='items'
                        label={text('Label', 'Items')}
                        items={[
                            {
                                label: 'Name',
                                attribute: 'name',
                                component: InputField,
                            },
                            {
                                label: 'Amount',
                                attribute: 'amount',
                                component: NumberField,
                            },
                        ]}
                    />
                </Form>
            ))(context)}
        </div>
    ));
