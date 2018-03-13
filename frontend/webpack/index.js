const webpack = require('webpack');
const api = require('./api');
const config = require('./config');
// const serve = require('webpack-serve');
const WebpackDevServer = require('webpack-dev-server');

// Publish api
module.exports = api;

// Auto start after define config
setTimeout(() => Promise.all(api._entries)
    .then(result => {
        const webpackConfig = config(
            api._config,
            Object.assign.apply(null, result)
        );

        // if (!api.isProduction()) {
            // new webpackDevServer(webpack(webpackConfig),{
            //     publicPath: webpackConfig.output.publicPath,
            //     hot: true,
            //     historyApiFallback: true
            // }).listen(5137,'localhost',(err,res)=>{
            //     if (err){
            //         return console.log(err);
            //     }
            //     console.log(`server is up on port 5137`);
            // });
        // }

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
            const devServer = new WebpackDevServer(compiler, webpackConfig.devServer);
            devServer.listen(5137,'localhost',(err)=>{
                if (err){
                    return console.log(err);
                }
                console.log(`server is up on port 5137`);
            });

        }
    })
    .catch(e => console.error(e)));