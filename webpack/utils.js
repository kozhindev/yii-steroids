const path = require('path');

module.exports = {

    generatePort() {
        return path.basename(process.cwd())
            .split('')
            .reduce((a, b) => {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, 0)
            .toString()
            .replace(/^-|/, '5')
            .replace(/([0-9]{4}).*/, '$1');
    },

    isProduction() {
        return process.argv.slice(2).filter(a => a.match(/(--)?production/) !== null).length > 0;
    },

};