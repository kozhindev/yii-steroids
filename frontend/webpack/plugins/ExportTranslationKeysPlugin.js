const ConstDependency = require('webpack/lib/dependencies/ConstDependency');
const NullFactory = require('webpack/lib/NullFactory');

const path = require('path');
const fs = require('fs');
const _ = require('lodash');
const translationKeys = {};

/**
 *
 * @param {object|function} localization
 * @param {object|string} Options object or obselete functionName string
 * @constructor
 */
class ExportTranslationKeysPlugin {
    constructor(localization, options, failOnMissing) {
        // Backward-compatiblility
        if (typeof options === 'string') {
            options = {
                functionName: options,
            };
        }

        if (typeof failOnMissing !== 'undefined') {
            options.failOnMissing = failOnMissing;
        }

        this.options = options || {};
        this.localization = null;
        this.functionName = this.options.functionName || '__';
        this.failOnMissing = !!this.options.failOnMissing;
        this.hideMessage = this.options.hideMessage || false;
    }

    apply(compiler) {
        const { localization, failOnMissing, hideMessage } = this; // eslint-disable-line no-unused-vars
        const name = this.functionName;

        compiler.plugin('compilation', (compilation, params) => { // eslint-disable-line no-unused-vars
            compilation.dependencyFactories.set(ConstDependency, new NullFactory());
            compilation.dependencyTemplates.set(ConstDependency, new ConstDependency.Template());
        });

        compiler.plugin('compilation', (compilation, data) => {
            data.normalModuleFactory.plugin('parser', (parser, options) => { // eslint-disable-line no-unused-vars
                // should use function here instead of arrow function due to save the Tapable's context
                parser.plugin(`call ${name}`, function exportTranslationKeysPlugin(expr) {
                    let param;
                    let defaultValue;
                    switch (expr.arguments.length) {
                        case 2:
                            param = this.evaluateExpression(expr.arguments[1]);
                            if (!param.isString()) return;
                            param = param.string;
                            defaultValue = this.evaluateExpression(expr.arguments[0]);
                            if (!defaultValue.isString()) return;
                            defaultValue = defaultValue.string;
                            break;
                        case 1:
                            param = this.evaluateExpression(expr.arguments[0]);
                            if (!param.isString()) return;
                            defaultValue = param = param.string;
                            break;
                        default:
                            return;
                    }
                    let result = localization ? localization(param) : defaultValue;

                    if (typeof result === 'undefined') {
                        let error = this.state.module[__dirname];
                        if (!error) {
                            error = new MissingLocalizationError(this.state.module, param, defaultValue);
                            this.state.module[__dirname] = error;

                            if (failOnMissing) {
                                this.state.module.errors.push(error);
                            } else if (!hideMessage) {
                                this.state.module.warnings.push(error);
                            }
                        } else if (!error.requests.includes(param)) {
                            error.add(param, defaultValue);
                        }
                        result = defaultValue;
                    }

                    // Find root entry
                    let module = this.state.module;
                    let bundlePath = null;
                    while(true) {
                        if (module.issuer && module.issuer.fileDependencies && module.issuer.fileDependencies[0]) {
                            module = module.issuer;
                        } else {
                            bundlePath = module.fileDependencies[0];
                            break;
                        }
                    }

                    // Find bundle name
                    const bundleName = bundlePath ? path.basename(bundlePath, '.js') : null;
                    if (bundleName) {
                        translationKeys[bundleName] = translationKeys[bundleName] || [];
                        translationKeys[bundleName].push(result);
                    }

                    return true;
                });
            });
        });

        compiler.plugin('done', (stats) => {
            Object.keys(translationKeys).forEach(bundleName => {
                const filePath = stats.compilation.outputOptions.path + '/assets/' + bundleName + '-lang.json';
                const arrayKeys = JSON.stringify(_.uniq(translationKeys[bundleName]));
                fs.writeFileSync(filePath, arrayKeys);
            });

        });
    }
}

module.exports = ExportTranslationKeysPlugin;