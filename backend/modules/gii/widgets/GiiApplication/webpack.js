const path = require('path');

require('../../../../../webpack')
    .config({
        outputPath: __dirname,
        staticPath: '',
        webpack: {
            resolve: {
                alias: {
                    components: __dirname + '/../../../../../components',
                    reducers: __dirname + '/../../../../../reducers',
                    'yii-steroids': __dirname + '/../../../../..',
                },
            },
        },
        modules: [
            path.resolve(__dirname, '../../../../../node_modules'),
        ],
    })
    .base('./index.js')
    .widgets('..');
