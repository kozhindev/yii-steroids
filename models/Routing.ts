import LocationModel from './Location';
import RouteModel from './Route';

export default interface RoutingModel {
    location: LocationModel;
    action: string | null;
    routes: Array<RouteModel>;
}