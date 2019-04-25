export default interface NavItemModel {
    id: string;
    label: string;
    title: string;
    url: string;
    icon: string | null;
    isActive: boolean;
    isVisible: boolean | null;
}