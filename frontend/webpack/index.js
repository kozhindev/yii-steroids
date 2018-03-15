const webpack = require('webpack');
const api = require('./api');
const getConfigMain = require('./config.main');
const getConfigDevServer = require('./config.devServer');
const WebpackDevServer = require('webpack-dev-server');

// Publish api
module.exports = api;

// Auto start after define config
setTimeout(() => Promise.all(api._entries)
    .then(result => {
        const webpackConfig = getConfigMain(
            api._config,
            Object.assign.apply(null, result)
        );

        // Init webpack compiler
        const compiler = webpack(webpackConfig);

        if (api.isProduction()) {
            // Production
            compiler.run((err, stats) => {
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
        } else {
            const devServerConfig = getConfigDevServer(api._config);
            console.log(`Listening at http://${devServerConfig.host}:${devServerConfig.port}`);

            // Development
            new WebpackDevServer(compiler, devServerConfig)
                .listen(devServerConfig.port, devServerConfig.host, (err) => {
                    if (err) {
                        return console.log(err);
                    }
                });

        }
    })
    .catch(e => console.error(e)));