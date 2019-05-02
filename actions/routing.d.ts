import { TYPE_ROUTING_REGISTER } from './actionTypes.d';
import RoutesTreeItemModel from '../models/RoutesTreeItem';

export interface IntRegisterRoutes {
    type: TYPE_ROUTING_REGISTER;
    routes: Array<RoutesTreeItemModel>;
}