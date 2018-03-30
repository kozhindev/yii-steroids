import IntlMessageFormat from 'intl-messageformat';
import moment from 'moment';
import 'moment/locale/ru';

// Fix load locale data
window.IntlMessageFormat = IntlMessageFormat;
require('intl-messageformat/dist/locale-data/ru');
delete window.IntlMessageFormat;

/**
 * @example
 *  {__('{count} {count, plural, one{день} few{дня} many{дней}}', {count: 2})}
 */
export default class LocaleComponent {

    constructor() {
        this.language = 'en';
        this.backendTimeZone = null;
        this.translations = {};

        // Publish to global
        window.__ = this.translate.bind(locale);
    }

    moment(date, format) {
        if (this.backendTimeZone && date && date.length === 19 && moment(date, 'YYYY-MM-DD HH:mm:ss').isValid()) {
            date = date + this.backendTimeZone;
        }
        return moment(date, format).locale(this.language);
    }

    t(message, params = {}) {
        return this.translate(message, params);
    }

    translate(message, params = {}) {
        // Translate
        message = this.translations[message] || message;

        // Format message (params, plural, etc..)
        const formatter = new IntlMessageFormat(message, this.language);
        message = formatter.format(params);

        return message;
    }
}