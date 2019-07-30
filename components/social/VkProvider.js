import queryString from 'query-string';

import {http} from 'components';

export default class VkProvider {

    constructor() {
        this.clientId = '';
    }

    async init() {
        return Promise.resolve();
    }

    async start() {
        return new Promise((resolve ,reject) => {
            // Generate url
            const url = 'https://oauth.vk.com/authorize/?' + queryString.stringify({
                client_id: this.clientId,
                redirect_uri: http.getUrl('/api/v1/auth/social/proxy'),
                scope: 'offline,public_profile',
                response_type: 'code',
            });

            // Open popup auth window
            const width = 655;
            const height = 600;
            const params = {
                toolbar: 'no',
                location: 'no',
                directories: 'no',
                status: 'no',
                menubar: 'no',
                scrollbars: 'no',
                resizable: 'no',
                width,
                height,
                left: (window.screen.width / 2) - (width / 2),
                top: (window.screen.height / 2) - (height / 2),
            };
            const popup = window.open(
                url,
                __('Авторизация через VK'),
                Object.entries(params).map(([key, value]) => key + '=' + value).join(',')
            );
            popup.onbeforeunload = () => reject();

            // This is a handler which is used by child window to pass auth result
            window.authCallback = link => {
                const params = (new URL(link)).searchParams;
                const error = params.get('error');
                if (error) {
                    reject(error);
                } else {
                    resolve({
                        token: params.get('code'),
                    });
                }
            };
        });
    }

}

