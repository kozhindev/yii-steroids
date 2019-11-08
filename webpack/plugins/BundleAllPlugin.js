'use strict';

/**
 * @param {Object} options
 * @constructor
 */
function BundleAllPlugin(options) {
    options = options || {};
    this.staticPath = options.staticPath || false;
}

BundleAllPlugin.prototype.apply = function(compiler) {

    const staticPath = this.staticPath;

    compiler.hooks.emit.tap('BundleAllPlugin', function (compilation) {
        const pathsOfBundles = [];
        const publicPath = compilation.outputOptions.publicPath;

        for (let assetName in compilation.assets) {

            if (/bundle-/.test(assetName)) {
                pathsOfBundles.push(publicPath + assetName);
            }
        }

        const scripts = pathsOfBundles.map(path => {
            if (/.js$/.test(path)) {
                return `<script async src=${path}><\/script>`;
            }

            if (/.css$/.test(path)) {
                return `<link href=${path} rel="stylesheet">`;
            }

            return null;
        }).filter(Boolean);

        const bundleAll = `document.write(\'${scripts.join('')}\');`;

        compilation.assets[`${staticPath}/bundle-all.js`] = {
            source: function() {
                return bundleAll;
            },
            size: function() {
                return bundleAll.length;
            }
        };
    });
};

module.exports = BundleAllPlugin;
