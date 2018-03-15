import ViewComponent from './ViewComponent';
import HtmlComponent from "./HtmlComponent";
//import store from '../store';

const components = {};
export const view = components.view = new ViewComponent();
export const html = components.html = new HtmlComponent();

// Apply configuration
const customConfig = {};//store.getState().config || {};
Object.keys(components).forEach(name => ({
    ...components[name],
    ...customConfig[name],
}));