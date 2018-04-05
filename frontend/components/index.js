import _merge from 'lodash-es/merge';

import ClientStorageComponent from './ClientStorageComponent';
import HtmlComponent from './HtmlComponent';
import HttpComponent from './HttpComponent';
import LocaleComponent from './LocaleComponent';
import ResourceComponent from './ResourceComponent';
import StoreComponent from './StoreComponent';
import UiComponent from './UiComponent';
import WidgetComponent from './WidgetComponent';

// Create instances
export const clientStorage = new ClientStorageComponent();
export const html = new HtmlComponent();
export const http = new HttpComponent();
export const locale = new LocaleComponent();
export const resource = new ResourceComponent();
export const store = new StoreComponent();
export const ui = new UiComponent();
export const widget = new WidgetComponent();

// Apply configuration
const customConfig = store.getState().config || {};
Object.keys(exports).forEach(name => {
    _merge(
        exports[name],
        customConfig[name] || {}
    );
});