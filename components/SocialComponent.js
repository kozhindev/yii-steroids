import providers from './social';

export default class SocialComponent {

    constructor() {
        this.providers = {};

        this._initializing = {};
    }

    init() {
        Object.keys(this.providers).forEach(name => {
            if (providers[name]) {
                const config = this.providers[name] || {};
                this.providers[name] = new providers[name]();
                Object.assign(this.providers[name], config);
            }
        });

        Object.keys(this.providers).forEach(name => {
            this._initializing[name] = this.providers[name].init();
        });
    }

    start(socialName) {
        if (!this._initializing[socialName]) {
            return Promise.reject();
        }

        return this._initializing[socialName]
            .then(() => this.providers[socialName].start());
    }


}
