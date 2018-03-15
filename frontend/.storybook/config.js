import {configure} from '@storybook/react';
import {view} from 'components';

// automatically import all views
view.add(require.context('../ui', true, /View.js$/));

// automatically import all files ending in *.stories.js
const reqStory = require.context('../ui', true, /.story.js$/);
configure(() => reqStory.keys().forEach(fileName => reqStory(fileName)), module);
