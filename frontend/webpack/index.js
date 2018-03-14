const webpack = require('webpack');
const api = require('./api');
const config = require('./config');
// const serve = require('webpack-serve');
const WebpackDevServer = require('webpack-dev-server');

// Publish api
module.exports = api;

// Disable auto start for storybook mode
if (process.argv[1].indexOf('@storybook') !== -1) {
    return;
}

// Auto start after define config
setTimeout(() => Promise.all(api._entries)
    .then(result => {
        const webpackConfig = config(
            api._config,
            Object.assign.apply(null, result)
        );

        // Init webpack compiler
        const compiler = webpack(webpackConfig);

        const handler = (err, stats) => {
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
        };

        if (api.isProduction()) {
            compiler.run(handler);
        } else {
            new WebpackDevServer(compiler, webpackConfig.devServer)
                .listen(webpackConfig.devServer.port, webpackConfig.devServer.host, (err) => {
                    if (err) {
                        return console.log(err);
                    }
                    console.log(`Listening at http://${webpackConfig.devServer.host}:${webpackConfig.devServer.port}`);
                });

        }
    })
    .catch(e => console.error(e)));