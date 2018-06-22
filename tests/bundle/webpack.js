const path = require('path');

require('../../webpack')
    .base('./app/*/client.js')
    .config({
        webpack: {
            resolve: {
                modules: [
                    path.resolve(process.cwd(), '../../node_modules'),
                    path.resolve(process.cwd(), 'app'),
                ],
            },
        }
    })
    .styles('./app/*/style/index.scss')
    .widgets('./app/*/widgets')