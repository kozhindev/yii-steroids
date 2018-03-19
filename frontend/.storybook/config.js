import React from 'react';
import {Provider} from 'react-redux';
import {configure, addDecorator} from '@storybook/react';
import {view, store} from 'components';
import { setDefaults } from '@storybook/addon-info';
import { setOptions } from '@storybook/addon-options';
import 'bootstrap/scss/bootstrap.scss'


//global options
setOptions({
    showAddonPanel: true,
    downPanelInRight: true,
});

//global options for addon-info
setDefaults({
    inline: true,
});

//wrapper for all stoies
addDecorator(getStory => (
    <Provider store={store.store}>
        <div style={{padding: '20px'}}>
            {getStory()}
        </div>
    </Provider>
));

// automatically import all views
view.add(require.context('../ui', true, /View.js$/));

// automatically import all files ending in *.stories.js
const reqStory = require.context('../ui', true, /.story.js$/);
configure(() => reqStory.keys().forEach(fileName => reqStory(fileName)), module);
