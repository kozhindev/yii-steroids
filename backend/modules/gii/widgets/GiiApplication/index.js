import {ui} from 'components';

// Automatically import all views
ui.addViews(require.context('yii-steroids/ui', true, /View.js$/));
ui.addFields(require.context('yii-steroids/ui', true, /Field.js$/));
ui.addFormatters(require.context('yii-steroids/ui', true, /Formatter.js$/));
