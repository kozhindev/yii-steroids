const path = require('path');
const fs = require('fs');
const _values = require('lodash/values');
const _merge = require('lodash/merge');
const utils = require('./utils');
const getConfigDefault = require('./config.default');

module.exports = (config) => {
    config = _merge(getConfigDefault(), config);

    // Init default webpack config
    let webpackConfig = {
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
                md: {
                    test: /\.md$/,
                    use: [
                        {
                            loader: 'html-loader',
                        },
                        {
                            loader: 'markdown-loader',
                        },
                    ],
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
    };

    // Merge with custom
    webpackConfig = _merge(
        webpackConfig,
        config.storybook
    );

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

    return webpackConfig;
};
