const utils = require('./utils');
const path = require('path');

module.exports = () => {
    return {
        cwd: process.cwd(),
        host: '127.0.0.1',
        port: utils.generatePort(),
        outputPath: path.resolve(process.cwd(), 'public'),
        staticPath: !utils.isProduction() ? 'static/1.0/' : '',
        webpack: {}, // you custom webpack config
        devServer: {}, // you custom dev server config
    };
};
