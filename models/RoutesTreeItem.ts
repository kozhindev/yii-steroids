import {RouteProps} from 'react-router';
import RouteModel from './Route';

export default interface RoutesTreeItem extends RouteProps {
    id: string;
    exact: boolean;
    path: string;
    componentProps?: {
        [key: string]: string,
    };
    label: string;
    title: string;
    roles?: Array<string>;
    icon: string | null;
    items?: Array<RouteModel>;
}