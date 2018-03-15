const webpack = require('webpack');
const path = require('path');
const fs = require('fs');
const _values = require('lodash/values');
const _merge = require('lodash/merge');
const utils = require('./utils');
const getConfigDefault = require('./config.default');

module.exports = (config, entry) => {
    config = _merge(getConfigDefault(), config);

    const webpackVersion = 3;

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
                                    utils.isProduction() && 'minify'
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
                    use: {
                        style: {
                            loader: 'style-loader',
                        },
                        css: {
                            loader: 'css-loader',
                        },
                        less: {
                            loader: 'less-loader',
                        },
                    },
                },
                sass: {
                    test: /\.scss$/,
                    use: {
                        style: {
                            loader: 'style-loader',
                        },
                        css: {
                            loader: 'css-loader',
                        },
                        sass: {
                            loader: 'sass-loader',
                        },
                    }
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
                path.resolve(config.cwd, 'app'),
            ],
        },
        plugins: [
            utils.isProduction() && new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                }
            }),
            utils.isProduction() && new webpack.optimize.OccurrenceOrderPlugin(),
            !utils.isProduction() && new webpack.ProgressPlugin(),
            !utils.isProduction() && new webpack.NamedModulesPlugin(),
            !utils.isProduction() && new webpack.HotModuleReplacementPlugin(),
            webpackVersion === 3 && utils.isProduction() && new webpack.optimize.UglifyJsPlugin({compress: {warnings: false}, sourceMap: false})
        ].filter(Boolean),
    };

    if (webpackVersion === 4) {
        webpackConfig = _merge(webpackConfig, {
            mode: utils.isProduction() ? 'production' : 'development',
            optimization: {
                splitChunks: {
                    cacheGroups: {
                        vendor: {
                            test: /node_modules/,
                            name: 'vendor',
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
    webpackConfig = _merge(webpackConfig, config.webpack);

    // Normalize rules (objects -> arrays)
    webpackConfig.module.rules = Object.keys(webpackConfig.module.rules)
        .map(key => {
            const item = webpackConfig.module.rules[key];
            if (item.use) {
                item.use = _values(item.use).filter(Boolean);
            }

            return item;
        })
        .filter(Boolean);

    // Add hot replace to each bundles
    if (!utils.isProduction()) {
        Object.keys(webpackConfig.entry).map(key => {
            webpackConfig.entry[key].unshift(`webpack-dev-server/client?http://${config.host}:${config.port}`, 'webpack/hot/dev-server');
        });
    }

    return webpackConfig;
};
