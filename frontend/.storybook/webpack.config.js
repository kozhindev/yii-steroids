const path = require('path');

module.exports = require('../webpack/config.storybook')({
    webpack: {
        resolve: {
            alias: {
                actions: path.resolve(__dirname, '../actions'),
                components: path.resolve(__dirname, '../components'),
                reducers: path.resolve(__dirname, '../reducers'),
            },
        },
    },
});
