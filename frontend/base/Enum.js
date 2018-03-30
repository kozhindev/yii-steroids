export default class Enum {

    static getLabels() {
        return {};
    }

    static getKeys() {
        return Object.keys(this.getLabels());
    }

    static getLabel(id) {
        return this.getLabels()[id] || '';
    }

    static getCssClasses() {
        return {};
    }

    static getCssClass(id) {
        return this.getCssClasses()[id] || '';
    }

}