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
const clientStorage = new ClientStorageComponent();
const html = new HtmlComponent();
const http = new HttpComponent();
const locale = new LocaleComponent();
const resource = new ResourceComponent();
const store = new StoreComponent();
const ui = new UiComponent();
const widget = new WidgetComponent();

// Apply configuration
const customConfig = store.getState().config || {};
_merge(clientStorage, customConfig.clientStorage);
_merge(html, customConfig.html);
_merge(http, customConfig.http);
_merge(locale, customConfig.locale);
_merge(resource, customConfig.resource);
_merge(store, customConfig.store);
_merge(ui, customConfig.ui);
_merge(widget, customConfig.widget);

export {
    clientStorage,
    html,
    http,
    locale,
    resource,
    store,
    ui,
    widget,
};