import React from 'react';
import fs from 'fs';
import path from 'path';
import {Provider} from 'react-redux';
import IntlMessageFormat from 'intl-messageformat';
import {renderToString} from 'react-dom/server';

import template from './template';

global.window = {};
global.location = {};
global.IntlMessageFormat = IntlMessageFormat;
process.env.IS_NODE = true;

const render = (defaultConfig, routes, assets, request) => {

    const store = require('components').store;
    const {walkRoutesRecursive} = require('../../ui/nav/navigationHoc');
    store.init({
        initialState: {
            navigation: {
                routesTree: walkRoutesRecursive(routes),
            },
        },
        history: {
            initialEntries: [
                request.url,
            ],
        },
    });

    const appPath = path.join(defaultConfig.sourcePath, 'Application.js');
    const Application = fs.existsSync(appPath) ? require(appPath).default : null;
    if (!Application) {
        return 'Not found Application component in ' + appPath;
    }

    return template(
        renderToString((
            <Provider store={store.store}>
                <Application/>
            </Provider>
        )),
        store.getState(),
        assets.filter(asset => /\.css/.test(asset.name)),
        assets.filter(asset => /\.js/.test(asset.name)),
    );
};

export default (app, defaultConfig, getStats) => {
    app.get('*', (request, response, next) => {
        // Skip for webpack dev server
        if (/^\/sockjs-node/.test(request.url)) {
            next();
            return;
        }

        let content = '';

        const routesPath = path.join(defaultConfig.sourcePath, 'routes', 'index.js');
        const routes = fs.existsSync(routesPath) ? require(routesPath) : null;
        if (routes) {
            const stats = getStats();
            if (stats) {
                const assets = stats.toJson({all: false, assets: true}).assets
                    .filter(asset => asset.chunks.includes('index') || asset.chunks.includes('common'));

                content = render(defaultConfig, routes, assets, request);
            }
        }

        console.log(77777);
        response.writeHead(200, {'Content-Type': 'text/html'});
        response.end(content);
    });
};
