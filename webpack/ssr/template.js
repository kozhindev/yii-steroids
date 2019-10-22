export default (html, state, cssAssets, jsAssets) => {
    const css = cssAssets.map(asset => `<link rel="stylesheet" href="/${asset.name}">`).join('\n');
    const js = jsAssets.map(asset => `<script src="/${asset.name}"></script>`).join('\n');

    return `
    <!DOCTYPE html>
    <html lang="ru">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no" />
            <title></title>
            ${css}
        </head>
        <body>
            <noscript>You need to enable JavaScript to run this app.</noscript>
            <div id="root">${html}</div>
            <script>
                window.APP_REDUX_PRELOAD_STATES = ${JSON.stringify([state])}
            </script>
            ${js}
        </body>
    </html>`;
};
