export default interface MetaModel {
    [metaKey: string]: {
        labels: Array<any>;
        fields: Array<any>;
        formatters?: Array<any>;
    }
}