const _merge = require('lodash/merge');
const getConfigDefault = require('./config.default');

module.exports = (config) => {
    config = _merge(getConfigDefault(), config);

    let devServerConfig = {
        contentBase: config.outputPath,
        hot: true,
        inline: true,
        historyApiFallback: true,
        port: config.port,
        host: config.host,
        disableHostCheck: true,
        headers: {
            'Host': config.host,
            'Access-Control-Allow-Origin': '*'
        },
        proxy: {
            '**': `http://${config.host}`,
        },
        staticOptions: {
            '**': `http://${config.host}`,
        },
        stats: {
            chunks: false,
            colors: true
        },
    };

    // Merge with custom
    devServerConfig = _merge(devServerConfig, config.devServer);

    return devServerConfig;
};
