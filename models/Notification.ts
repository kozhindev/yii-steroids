export default interface NotificationModel {
    id: number;
    level: string;
    message: string;
    isClosing: boolean;
}

export default interface NotificationsModel {
    [key: string]: NotificationModel;
}