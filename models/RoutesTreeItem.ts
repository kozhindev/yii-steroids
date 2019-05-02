import { RouteProps } from 'react-router';

export default interface RoutesTreeItemModel extends RouteProps {
    id: string;
    isVisible: boolean;
    path: string;
    componentProps?: {
        [key: string]: string,
    };
    label: string;
    title: string;
    roles?: Array<string>;
    icon?: string | null;
    items?: Array<RoutesTreeItemModel>;
}