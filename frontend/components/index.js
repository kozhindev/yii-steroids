import ViewComponent from './ViewComponent';
//import store from '../store';

export const view = new ViewComponent();

// Apply configuration
const customConfig = {};//store.getState().config || {};
Object.keys(exports).forEach(name => ({
    ...exports[name],
    ...customConfig[name],
}));