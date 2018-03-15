import React from 'react';
import {Provider} from 'react-redux';
import {configure, addDecorator} from '@storybook/react';
import {view, store} from 'components';

addDecorator(getStory => (
    <Provider store={store.store}>
        {getStory()}
    </Provider>
));

// automatically import all views
view.add(require.context('../ui', true, /View.js$/));

// automatically import all files ending in *.stories.js
const reqStory = require.context('../ui', true, /.story.js$/);
configure(() => reqStory.keys().forEach(fileName => reqStory(fileName)), module);
