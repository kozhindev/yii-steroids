import ListItemModel from './ListItem';

//TODO: replace "any" and "object"
export default interface ListModel {
    meta: null;
    selectedIds: Array<string>;
    total: number;
    isFetched: boolean;
    isLoading: boolean;
    action: string;
    actionMethod: string;
    page: number;
    pageSize: number;
    sort: null;
    query: object;
    loadMore: boolean;
    primaryKey: string;
    listId: string;
    type: string;
    items: Array<ListItemModel<any>>;
}