const webpack = require('webpack');
const path = require('path');
const fs = require('fs');
const _ = require('lodash');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const ExportTranslationKeysPlugin = require('./plugins/ExportTranslationKeysPlugin');

const utils = require('./utils');
const getConfigDefault = require('./config.default');

module.exports = (config, entry) => {
    config = _.merge(getConfigDefault(), config);

    const webpackVersion = 3;

    // For split chunks
    if (webpackVersion === 4) {
        const indexEntry = entry.index;
        delete entry.index;
        // TODO
    }

    // Init default webpack config
    let webpackConfig = {
        entry,
        devtool: !utils.isProduction() ? 'eval-source-map' : false,
        output: utils.isProduction()
            ? {
                publicPath: '/',
                path: config.outputPath,
                filename: 'assets/bundle-[name].js',
                chunkFilename: 'assets/bundle-[name].js',
            }
            : {
                publicPath: `http://${config.host}:${config.port}/`,
                path: config.outputPath,
                filename: `${config.staticPath}assets/bundle-[name].js`,
                chunkFilename: `${config.staticPath}assets/bundle-[name].js`,
            },
        module: {
            rules: {
                js: {
                    test: /\.js$/,
                    use: {
                        babel: {
                            loader: 'babel-loader',
                            options: {
                                cacheDirectory: true,
                                plugins: [
                                    'transform-decorators-legacy',
                                    'transform-class-properties',
                                    'transform-object-rest-spread',
                                    'transform-export-extensions',
                                    utils.isProduction() && 'transform-runtime',
                                    !utils.isProduction() && 'react-hot-loader/babel',
                                ].filter(Boolean),
                                presets: [
                                    'env',
                                    'react',
                                    webpackVersion === 4 && utils.isProduction() && 'minify'
                                ].filter(Boolean),
                            }
                        },
                        eslint: !utils.isProduction() && fs.existsSync(config.cwd + '/.eslintrc') && {
                            loader: 'eslint-loader',
                            options: {
                                configFile: config.cwd + '/.eslintrc',
                            }
                        },
                    },
                    exclude: /node_modules(\/|\\+)(?!yii-steroids)/,
                },
                json: {
                    test: /\.json$/,
                    use: {
                        json: 'json-loader'
                    },
                },
                less: {
                    test: /\.less$/,
                    use: ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        use: ['css-loader', 'less-loader']
                    }),
                },
                sass: {
                    test: /\.scss$/,
                    use: ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        use: ['css-loader', 'sass-loader']
                    }),
                },
                font: {
                    test: /(\/|\\)fonts(\/|\\).*\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                    use: {
                        file: {
                            loader: 'file-loader',
                            options: {
                                name: 'fonts/[name].[ext]'
                            },
                        },
                    },
                },
                image: {
                    test: /\.(jpe?g|gif|png|svg)$/,
                    use: {
                        file: {
                            loader: 'file-loader'
                        },
                    },
                },
            },
        },
        resolve: {
            extensions: ['.js'],
            alias: {
                app: path.resolve(config.cwd, 'app'),
                actions: 'core/frontend/actions',
                components: 'core/frontend/components',
                reducers: 'core/frontend/reducers',
                shared: 'core/frontend/shared',
            },
            modules: [
                path.resolve(config.cwd, 'node_modules'), // the old 'fallback' option (needed for npm link-ed packages)
                fs.existsSync(path.resolve(config.cwd, '../node_modules')) ? path.resolve(config.cwd, '../node_modules') : null,
                path.resolve(config.cwd, 'app'),
            ].filter(Boolean),
        },
        plugins: [
            new ExportTranslationKeysPlugin(),
            utils.isProduction() && new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                }
            }),
            new ExtractTextPlugin({
                //filename: `${config.staticPath}assets/bundle-style.css`,
                filename:  (getPath) => {
                    return `${config.staticPath}assets/bundle-` + getPath('[name].css');
                },
                allChunks: true
            }),
            utils.isProduction() && new webpack.optimize.OccurrenceOrderPlugin(),
            !utils.isProduction() && new webpack.ProgressPlugin(),
            !utils.isProduction() && new webpack.NamedModulesPlugin(),
            !utils.isProduction() && new webpack.HotModuleReplacementPlugin(),
            webpackVersion === 3 && new webpack.optimize.CommonsChunkPlugin({name: 'index', filename: `${config.staticPath}assets/bundle-index.js`}),
            webpackVersion === 3 && utils.isProduction() && new webpack.optimize.UglifyJsPlugin({compress: {warnings: false}, sourceMap: false})
        ].filter(Boolean),
    };

    if (webpackVersion === 4) {
        webpackConfig = _.merge(webpackConfig, {
            mode: utils.isProduction() ? 'production' : 'development',
            optimization: {
                splitChunks: {
                    cacheGroups: {
                        index: {
                            test: indexEntry,
                            name: 'index',
                            chunks: 'initial',
                            enforce: true
                        }
                    }
                },
                minimize: utils.isProduction(),
            }
        });
    }

    // Merge with custom
    webpackConfig = _.merge(webpackConfig, config.webpack);

    // Normalize rules (objects -> arrays)
    webpackConfig.module.rules = Object.keys(webpackConfig.module.rules)
        .map(key => {
            const item = webpackConfig.module.rules[key];
            if (item.use) {
                item.use = _.values(item.use).filter(Boolean);
            }

            return item;
        })
        .filter(Boolean);

    // Add hot replace to each bundles
    if (!utils.isProduction()) {
        Object.keys(webpackConfig.entry).map(key => {
            webpackConfig.entry[key] = []
                .concat([
                    `webpack-dev-server/client?http://${config.host}:${config.port}`,
                    'webpack/hot/dev-server',
                ])
                .concat(webpackConfig.entry[key])
        });
    }

    return webpackConfig;
};
