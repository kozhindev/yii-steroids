import IntlMessageFormat from 'intl-messageformat';
import moment from 'moment';
import 'moment/locale/ru';
import 'moment/locale/it';

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
        this.sourceLanguage = 'ru';
        this.backendTimeZone = null;
        this.translations = {};

        // Publish to global
        window.__ = this.translate.bind(this);
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
        const language = this.translations[message] ? this.language : this.sourceLanguage;
        const formatter = new IntlMessageFormat(message, language);
        message = formatter.format(params);

        return message;
    }
}