import cookie from 'js-cookie';

export default class ClientStorageComponent {

    constructor() {
        this.localStorageAvailable = !process.env.IS_SSR;
        this.sessionStorageAvailable = !process.env.IS_SSR;
        this.cookieAvailable = !process.env.IS_SSR;

        this.STORAGE_SESSION = 'session';
        this.STORAGE_LOCAL = 'local';
        this.STORAGE_COOKIE = 'cookie';

        if (this.localStorageAvailable) {
            try {
                window.localStorage.setItem('localStorageAvailable', true);
                window.localStorage.removeItem('localStorageAvailable');
            } catch (e) {
                this.localStorageAvailable = false;
            }
        }

        if (this.sessionStorageAvailable) {
            try {
                window.sessionStorage.setItem('sessionStorageAvailable', true);
                window.sessionStorage.removeItem('sessionStorageAvailable');
            } catch (e) {
                this.sessionStorageAvailable = false;
            }
        }

        if (this.cookieAvailable) {
            try {
                cookie.set('cookieAvailable', true, {
                    domain: this._getDomain(),
                });
                const cookieAvailable = cookie.get('cookieAvailable', {
                    domain: this._getDomain()
                });

                if (!cookieAvailable) {
                    this.cookieAvailable = false;
                }

                cookie.remove('cookieAvailable', {
                    domain: this._getDomain()
                });

            } catch (e) {
                this.cookieAvailable = false;
            }
        }
    }

    /**
     * @param {string} name
     * @param {string} [storageName]
     * @returns {*}
     */
    get(name, storageName) {
        storageName = storageName || this.STORAGE_LOCAL;

        if (storageName === this.STORAGE_LOCAL && this.localStorageAvailable) {
            return window.localStorage.getItem(name);
        } else if (storageName === this.STORAGE_SESSION && this.sessionStorageAvailable) {
            return window.sessionStorage.getItem(name);
        } else if (storageName === this.STORAGE_COOKIE && this.cookieAvailable) {
            return cookie.get(name);
        }
        return window.localStorage.getItem(name);
    }

    /**
     * @param {string} name
     * @param {*} value
     * @param {string} [storageName]
     * @param {number|null} [expires]
     */
    set(name, value, storageName, expires = null) {
        storageName = storageName || this.STORAGE_LOCAL;

        if (storageName === this.STORAGE_LOCAL && this.localStorageAvailable) {
            window.localStorage.setItem(name, value);
        } else if (storageName === this.STORAGE_SESSION && this.sessionStorageAvailable) {
            window.sessionStorage.setItem(name, value);
        } else if (storageName === this.STORAGE_COOKIE && this.cookieAvailable) {
            cookie.set(name, value, {
                expires,
                domain: this._getDomain(),
            });
        } else {
            window.localStorage.setItem(name, value);
        }
    }

    /**
     *
     * @param {string} name
     * @param {string} [storageName]
     */
    remove(name, storageName) {
        storageName = storageName || this.STORAGE_LOCAL;

        if (storageName === this.STORAGE_LOCAL && this.localStorageAvailable) {
            window.localStorage.removeItem(name);
        } else if (storageName === this.STORAGE_SESSION && this.sessionStorageAvailable) {
            window.sessionStorage.removeItem(name);
        } else if (storageName === this.STORAGE_COOKIE && this.cookieAvailable) {
            cookie.remove(name, {
                domain: this._getDomain()
            });
        } else {
            window.localStorage.removeItem(name);
        }
    }

    _getDomain() {
        const host = typeof location !== 'undefined' && location.hostname || '';
        return !/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/.test(host) && host.split('.').slice(-2).join('.') || host;
    }

}
