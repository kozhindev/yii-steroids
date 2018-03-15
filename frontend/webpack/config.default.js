const utils = require('./utils');
const path = require('path');

module.exports = () => {
    return {
        cwd: process.cwd(),
        host: 'localhost',
        port: utils.generatePort(),
        outputPath: path.resolve(process.cwd(), 'public'),
        staticPath: utils.isProduction() ? 'static/1.0/' : '',
        webpack: {}, // you custom webpack config
        devServer: {}, // you custom dev server config
    };
};
