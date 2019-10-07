const webpack = require('webpack');
const _ = require('lodash');
const express = require('express');
const WebpackDevServer = require('webpack-dev-server');

const api = require('./api');
const getConfigMain = require('./config.main');
const getConfigDefault = require('./config.default');
const getConfigDevServer = require('./config.devServer');

// Publish api
module.exports = api;

// Auto start after define config
setTimeout(() => Promise.all(api._entries)
    .then(result => {
        const webpackConfig = getConfigMain(
            api._config,
            Object.assign.apply(null, result)
        );
        const defaultConfig = _.merge(getConfigDefault(), api._config);

        // Init webpack compiler
        const compiler = webpack(webpackConfig);

        // Express app (for dev server and ssr)
        let expressApp = null;
        let getStats = null;
        let httpListen = null;

        if (api.isProduction()) {
            // Production
            let _stats = null;
            compiler.run((err, stats) => {
                _stats = stats;
                if (err) {
                    console.error(err);
                } else {
                    console.log(stats.toString({
                        chunks: false,
                        children: false,
                        colors: true,
                        publicPath: true,
                    }));
                }
            });
            getStats = () => _stats;
        } else {
            const devServerConfig = getConfigDevServer(api._config);
            if (api.isSSR()) {
                devServerConfig.features = [
                    'setup',
                    'before',
                    'headers',
                    'middleware',
                    'proxy',
                    //'contentBaseFiles',
                    //'historyApiFallback',
                    //'contentBaseFiles',
                    //'contentBaseIndex',
                    'magicHtml',
                ];
            }

            // Development
            const devServer = new WebpackDevServer(compiler, devServerConfig);
            expressApp = devServer.app;
            httpListen = devServer.listen.bind(devServer);
            getStats = () => devServer._stats;
        }

        if (api.isSSR()) {
            require('@babel/register')({
                presets: [
                    '@babel/preset-env',
                    '@babel/preset-react'
                ],
                plugins: [
                    ['@babel/plugin-proposal-decorators', {legacy: true}],
                    '@babel/plugin-proposal-class-properties',
                    '@babel/plugin-transform-runtime',
                    //'@babel/plugin-syntax-dynamic-import',
                    //'@babel/plugin-transform-modules-commonjs',
                    ['module-resolver', {
                        //root: webpackConfig.resolve.modules,
                        root: [defaultConfig.sourcePath],
                        alias: webpackConfig.resolve.alias,
                    }],
                    'require-context-hook',
                ],
                only: [
                    /lodash-es/,
                    /yii-steroids/,
                    defaultConfig.sourcePath,
                ],
            });
            require.extensions['.scss'] = () => {};
            require.extensions['.less'] = () => {};
            require('./ssr/require-context')();

            if (!expressApp) {
                expressApp = express();
                expressApp.use(express.static(defaultConfig.outputPath));
                httpListen = expressApp.listen.bind(expressApp);
            }
            require('./ssr/index').default(expressApp, defaultConfig, getStats);
        }

        if (expressApp) {
            console.log(`Listening at http://${defaultConfig.host}:${defaultConfig.port}`);
            httpListen(defaultConfig.port, defaultConfig.host, (err) => {
                if (err) {
                    return console.log(err);
                }
            });
        }
    })
    .catch(e => console.error(e)));
