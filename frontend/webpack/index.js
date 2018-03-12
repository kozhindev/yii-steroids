const webpack = require('webpack');
const api = require('./api');
const config = require('./config');
const serve = require('webpack-serve');

// Publish api
module.exports = api;

// Auto start after define config
setTimeout(() => Promise.all(api._entries)
    .then(result => {
        const webpackConfig = config(
            api._config,
            Object.assign.apply(null, result)
        );

        if (!api.isProduction()) {
            webpackConfig.entry.devServer = ['webpack-dev-server/client'];
        }

        // Init webpack compiler
        const compiler = webpack(webpackConfig);

        // Hook to exit on Ctrl+C
        ['SIGINT', 'SIGTERM'].forEach((sig) => {
            process.on(sig, () => {
                server.close(() => {
                    process.exit(); // eslint-disable-line no-process-exit
                });
            });
        });

        if (api.isProduction()) {
            compiler.run( (err, stats) => {
                if (err) {
                    console.error(err);
                } else {
                    console.log(stats.toString({
                        chunks: false,
                        colors: true,
                        publicPath: true,
                    }));
                }
            });
        } else {
            serve({
                compiler,
                port: webpackConfig.devServer.port,
                dev: webpackConfig.devServer,
            });
        }

    })
    .catch(e => console.error(e)));