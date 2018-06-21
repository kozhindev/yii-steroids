export default class Model {

    static meta() {
        return {};
    }

    static getMetaItem(attribute) {
        const meta = this.meta();
        return {
            appType: 'string',
            label: '',
            hint: '',
            required: false,
            ...meta[attribute],
        };
    }
}