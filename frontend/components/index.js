import _lowerFirst from 'lodash-es/lowerFirst';

// Load
const reqComponents = require.context('.', false, /Component.js$/);
reqComponents.keys().forEach(fileName => {
    const name = _lowerFirst(fileName.substr(2, fileName.length - 14));
    const ComponentClass = reqComponents(fileName).default;
    module.exports[name] = new ComponentClass();
});

// Configure
const customConfig = {};//store.getState().config || {};
Object.keys(module.exports).forEach(name => ({
    ...module.exports[name],
    ...customConfig[name],
}));