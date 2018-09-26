const webpack = require('webpack');
const path = require('path');
const fs = require('fs');
const _ = require('lodash');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const ExportTranslationKeysPlugin = require('./plugins/ExportTranslationKeysPlugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

const utils = require('./utils');
const getConfigDefault = require('./config.default');

function recursiveIssuer(m) {
    if (m.issuer) {
        return recursiveIssuer(m.issuer);
    } else if (m.name) {
        return m.name;
    } else {
        return false;
    }
}

module.exports = (config, entry) => {
    config = _.merge(getConfigDefault(), config);

    // For split chunks
    const indexEntry = entry.index;
    //delete entry.index;

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
                                    //'transform-object-rest-spread',
                                    //'transform-export-extensions',
                                    ['@babel/plugin-proposal-decorators', {legacy: true}],
                                    '@babel/plugin-proposal-class-properties',
                                    utils.isProduction() && 'transform-runtime',
                                    !utils.isProduction() && 'react-hot-loader/babel',
                                ].filter(Boolean),
                                presets: [
                                    '@babel/preset-env',
                                    '@babel/preset-react',
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
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'less-loader',
                    ],
                },
                sass: {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'sass-loader',
                    ],
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
            utils.isAnalyze() && new BundleAnalyzerPlugin(),
            new ExportTranslationKeysPlugin(),
            utils.isProduction() && new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: '"production"'
                }
            }),
            new MiniCssExtractPlugin({
                filename: `${config.staticPath}assets/bundle-[name].css`,
                chunkFilename: `${config.staticPath}assets/bundle-[id].css`,
            }),
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/), // Skip moment locale files (0.3 mb!)
            utils.isProduction() && new webpack.optimize.OccurrenceOrderPlugin(),
            !utils.isProduction() && new webpack.ProgressPlugin(),
            !utils.isProduction() && new webpack.NamedModulesPlugin(),
        ].filter(Boolean),
    };

    webpackConfig = _.merge(webpackConfig, {
        mode: utils.isProduction() ? 'production' : 'development',
        optimization: {
            runtimeChunk: {
                name: 'common',
            },
            minimize: utils.isProduction(),
        }
    });
    if (indexEntry) {
        webpackConfig.optimization.splitChunks = {
            cacheGroups: {
                common: {
                    name: 'common',
                    chunks: 'initial',
                    minChunks: 2,
                    minSize: 0,
                }
            }
        };
    }

    // Extracting CSS based on entry
    webpackConfig.optimization.splitChunks = webpackConfig.optimization.splitChunks || {cacheGroups: {}};
    Object.keys(entry).forEach(name => {
        // Skip styles
        if (/^style-/.test(name)) {
            return;
        }

        webpackConfig.optimization.splitChunks.cacheGroups[name] = {
            name: name,
            test: m => m.constructor.name === 'CssModule' && recursiveIssuer(m) === name,
            chunks: 'all'
        };
    });

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

    return webpackConfig;
};
