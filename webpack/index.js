const webpack = require('webpack');
const _ = require('lodash');
const fs = require('fs');
const path = require('path');
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
        let devServer = null;

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
                    //'middleware', - Will be run after ssr
                    'proxy',
                    //'contentBaseFiles',
                    //'historyApiFallback',
                    //'contentBaseFiles',
                    //'contentBaseIndex',
                    'magicHtml',
                ];
            }

            // Development
            devServer = new WebpackDevServer(compiler, devServerConfig);
            expressApp = devServer.app;
            httpListen = devServer.listen.bind(devServer);
            getStats = () => devServer._stats;
        }

        if (api.isSSR()) {
            console.log('SSR Enabled, source dir: ' + defaultConfig.sourcePath);
            require('@babel/register')(_.merge(
                {
                    extensions: ['.js', '.jsx', '.es6', '.es', '.mjs', '.ts', '.tsx'],
                    presets: [
                        '@babel/preset-env',
                        '@babel/preset-react',
                        '@babel/typescript',
                    ],
                    plugins: [
                        ['@babel/plugin-proposal-decorators', {legacy: true}],
                        '@babel/plugin-proposal-class-properties',
                        '@babel/plugin-transform-runtime',
                        ['module-resolver', {
                            //root: webpackConfig.resolve.modules,
                            root: [defaultConfig.sourcePath],
                            alias: webpackConfig.resolve.alias,
                            "extensions": ['.js', '.jsx', '.es6', '.es', '.mjs', '.ts', '.tsx'],
                        }],
                        'require-context-hook',
                    ],
                    only: [
                        /lodash-es|yii-steroids/,
                        defaultConfig.sourcePath,
                    ],
                    cache: api.isProduction(),
                },
                defaultConfig.ssr.register || {}
            ));
            // Ignore .css and other includes
            ['css', 'less', 'scss', 'sass']
                .forEach(ext => require.extensions['.' + ext] = () => {});
            ['ttf', 'woff', 'woff2', 'png', 'jpg', 'jpeg', 'gif']
                .forEach(ext => require.extensions['.' + ext] = (module, file) => {
                    const fileName = path.basename(file);
                    const assets = getStats().compilation.assets;
                    Object.keys(assets).find(publicUrl => {
                        let publicName = path.basename(publicUrl);
                        publicName = publicName.replace(new RegExp('\.?[a-z0-9]{32}\.' + ext), '.' + ext);

                        // TODO Логика соответствия по имени файла хрупкая и не всегда будет правильной. Но пока
                        // TODO не удалось достать полные пути исходного файла и публичного url
                        if (publicName === fileName) {
                            module.exports = '/' + _.trimStart(publicUrl, '/');
                            return true;
                        }
                        return false;
                    });
                    return module;
                });
            require.extensions['.svg'] = function(module, filename) {
                const svgStr = fs.readFileSync(filename, 'utf8');

                // TODO Structure this code
                // Code from https://github.com/webpack-contrib/svg-inline-loader/blob/master/index.js#L11
                var regexSequences = [
                    // Remove XML stuffs and comments
                    [/<\?xml[\s\S]*?>/gi, ""],
                    [/<!doctype[\s\S]*?>/gi, ""],
                    [/<!--.*-->/gi, ""],
                    // SVG XML -> HTML5
                    [/\<([A-Za-z]+)([^\>]*)\/\>/g, "<$1$2></$1>"], // convert self-closing XML SVG nodes to explicitly closed HTML5 SVG nodes
                    [/\s+/g, " "],                                 // replace whitespace sequences with a single space
                    [/\> \</g, "><"]                               // remove whitespace between tags
                ];
                // Clean-up XML crusts like comments and doctype, etc.
                module.exports = regexSequences.reduce(function (prev, regexSequence) {
                    return ''.replace.apply(prev, regexSequence);
                }, svgStr).trim();

                return module;
            };
            require('./ssr/require-context')();

            if (!expressApp) {
                expressApp = express();
                expressApp.use(express.static(defaultConfig.outputPath));
                httpListen = expressApp.listen.bind(expressApp);
            }
            require('./ssr/index').default(expressApp, defaultConfig, getStats);

            // Use devServer middleware after ssr
            if (devServer) {
                devServer.setupMiddleware();
            }
        }

        if (expressApp && httpListen) {
            console.log(`Listening at http://${defaultConfig.host}:${defaultConfig.port}`);
            httpListen(defaultConfig.port, defaultConfig.host, (err) => {
                if (err) {
                    return console.error(err);
                }
            });
        }
    })
    .catch(e => console.error(e)));
