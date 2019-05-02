export const ROUTING_REGISTER = 'ROUTING_REGISTER';
import RoutesTreeItemModel from '../models/RoutesTreeItem';

export const registerRoutes = (routes: Array<RoutesTreeItemModel>) => ({
    type: ROUTING_REGISTER,
    routes,
});
