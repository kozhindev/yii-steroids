<?php

namespace steroids\views;

use yii\web\View;

/** @type View $this */

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<script>
    if (window.opener && window.opener.authCallback) {
        window.opener.authCallback(window.location.href);
    }
    window.close();
</script>
</body>
</html>
