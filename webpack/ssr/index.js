import React from 'react';
import fs from 'fs';
import path from 'path';
import {parse} from 'url';
import {Provider} from 'react-redux';
import IntlMessageFormat from 'intl-messageformat';
import {renderToString} from 'react-dom/server';
import _merge from 'lodash/merge';

import template from './template';
import SsrProvider from '../../ui/nav/Router/SsrProvider';

global.window = {};
global.location = {};
global.IntlMessageFormat = IntlMessageFormat;
process.env.IS_SSR = true;

const renderReact = async (Application, store, history, staticContext, level = 0) => {
    const content = renderToString(
        <Provider>
            <SsrProvider
                store={store}
                history={history}
                staticContext={staticContext}
            >
                <Application/>
            </SsrProvider>
        </Provider>
    );

    const http = require('components').http;
    if (http._promises.length > 0 && level < 2) {
        await Promise.all(http._promises);
        http._promises = [];

        return renderReact(Application, store, history, staticContext,level + 1);
    }

    return content;
};

const renderContent = async (defaultConfig, routes, assets, request) => {
    const parsedUrl = parse(request.url);
    const location = {
        pathname: parsedUrl.pathname,
        search: parsedUrl.search,
        hash: parsedUrl.hash,
        key: '1r9orr'
    };

    const {walkRoutesRecursive, treeToList} = require('../../ui/nav/navigationHoc');
    const StoreComponent = require('../../components/StoreComponent').default;
    const store = new StoreComponent();
    store.init({
        initialState: _merge(
            {},
            defaultConfig.ssr.initialState || {},
            {
                config: {
                    http: {
                        apiUrl: process.env.APP_BACKEND_URL || '',
                    },
                },
                routing: {
                    location,
                    routes: treeToList(routes),
                },
                navigation: {
                    routesTree: walkRoutesRecursive(routes),
                },
            }
        ),
        history: {
            initialEntries: [
                request.url,
            ],
        },
    });
    const appPath = resolveFileExtension(path.join(defaultConfig.sourcePath, 'Application'));
    const Application = fs.existsSync(appPath) ? require(appPath).default : null;
    if (!Application) {
        return 'Not found Application component in ' + appPath;
    }

    const staticContext = {};

    // Temp render for fill store
    return template(
        await renderReact(Application, store.store, store.history, staticContext),
        store.getState(),
        assets.filter(asset => /\.css/.test(asset.name)),
        assets.filter(asset => /\.js/.test(asset.name)),
    );
};

const resolveFileExtension = path => {
    let result = null;
    ['js', 'ts', 'jsx', 'tsx', 'es6', 'es', 'mjs'].forEach(ext => {
        if (!result) {
            if (fs.existsSync(path + '.' + ext)) {
                result = path + '.' + ext;
            }
        }
    });
    return result;
};

export default (app, defaultConfig, getStats) => {
    app.get('*', async (request, response, next) => {
        // Skip for webpack dev server
        if (/^\/sockjs-node/.test(request.url) || /hot-update/.test(request.url)
            || /(jpe?g|gif|css|png|js|ico|xml|less|eot|svg|tff|woff2?|txt|map|mp4|ogg|webm|pdf|dmg|exe|html)$/.test(request.url)) {
            next();
            return;
        }

        let content = '';

        // Find routes tree
        const routesPath = resolveFileExtension(path.join(defaultConfig.sourcePath, 'routes', 'index'));
        const routes = fs.existsSync(routesPath) ? require(routesPath) : null;
        if (routes) {
            const stats = getStats();
            if (stats) {
                const assets = stats.toJson({all: false, assets: true}).assets
                    .filter(asset => asset.chunks.includes('index') || asset.chunks.includes('common'));

                try {
                    content = await renderContent(defaultConfig, routes, assets, request);
                } catch(e) {
                    console.error('Render error in url ' + request.url, e);
                }
            }
        }

        response.writeHead(200, {'Content-Type': 'text/html'});
        response.end(content);
    });
};
