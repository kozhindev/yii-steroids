export default class Model {

    static fields() {
        return {};
    }

    static formatters() {
        return {};
    }

    static getRequiredFields() {
        return Object.entries(this.fields())
            .filter(fieldEntry => fieldEntry[1].required)
            .map(fieldEntry => fieldEntry[0])
    }
}