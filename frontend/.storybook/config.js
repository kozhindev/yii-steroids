import React from 'react';
import {Provider} from 'react-redux';
import {configure, addDecorator} from '@storybook/react';
import {view, store} from 'components';
import {setDefaults} from '@storybook/addon-info';
import {setOptions} from '@storybook/addon-options';
import 'bootstrap/scss/bootstrap.scss'
import {withKnobs} from "@storybook/addon-knobs/react";

// Global options for addon-options
setOptions({
    showAddonPanel: true,
    downPanelInRight: true,
});

// Global options for addon-info
setDefaults({
    inline: true,
    propTables: null,
    maxPropsIntoLine: 1,
    styles: stylesheet => ({
        ...stylesheet,
        header: {
            ...stylesheet.header,
            h1: {
                display: 'none',
            },
            h2: {
                ...stylesheet.header.h2,
                fontWeight: 500,
                fontSize: '25px',
            },
        },
        infoBody: {
            ...stylesheet.infoBody,
            marginTop: 0,
            padding: 0,
            border: 0,
            boxShadow: '',
        },
        source: {
            ...stylesheet.source,
            h1: {
                opacity: 0,
                fontSize: '10px',
            },
        },
        propTableHead: {
            ...stylesheet.propTableHead,
            fontSize: 16,
            margin: '5px 0',
        },
    }),
});

// Add knobs addon
addDecorator(withKnobs);

// Wrapper for all stories
addDecorator(getStory => (
    <Provider store={store.store}>
        <div style={{padding: '20px'}}>
            {getStory()}
        </div>
    </Provider>
));

// Automatically import all views
view.add(require.context('../ui', true, /View.js$/));

// Automatically import all files ending in *.stories.js
const reqStory = require.context('../ui', true, /.story.js$/);
configure(() => reqStory.keys().forEach(fileName => reqStory(fileName)), module);
