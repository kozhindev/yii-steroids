import React from 'react';
import {storiesOf} from '@storybook/react';
import {withInfo} from '@storybook/addon-info';
import {object, array} from '@storybook/addon-knobs/react';

import List from './List';
import DropDownField from "../../form/DropDownField/DropDownField";
import DateField from "../../form/DateField/DateField";

class ItemView extends React.PureComponent {
    render() {
        return (
            <div className='p-3 mb-3 border border-info'>
               ItemView
            </div>
        );
    }
}

const dropDownItems = [
    {
        id: 1,
        label: 'First',
    },
    {
        id: 2,
        label: 'Second',
    },
    {
        id: 3,
        label: 'Third',
    },
    {
        id: 4,
        label: 'Fourth',
    },
];

const searchForm = {
    fields: [
        {
            label: 'DropDown',
            attribute: 'dropDown',
            component: DropDownField,
            items: dropDownItems,
            autoComplete: true,
        },
        {
            label: 'Date',
            attribute: 'date',
            component: DateField,
        },
    ],
};

storiesOf('List', module)
    .add('List', context => (
        <div>
            {withInfo()(() => (
                <List
                    listId='ListStory'
                    items={array('Items', [1, 2])}
                    itemView={ItemView}
                    searchForm={object('Search Form', searchForm)}
                />
            ))(context)}
        </div>
    ));