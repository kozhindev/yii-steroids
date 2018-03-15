import HtmlComponent from "./HtmlComponent";
import StoreComponent from "./StoreComponent";
import ViewComponent from './ViewComponent';

const components = {};
export const html = components.html = new HtmlComponent();
export const store = components.store = new StoreComponent();
export const view = components.view = new ViewComponent();

// Apply configuration
const customConfig = {};//store.getState().config || {};
Object.keys(components).forEach(name => ({
    ...components[name],
    ...customConfig[name],
}));