<?php
if (strstr($_SERVER['REQUEST_URI'], 'time=') !== FALSE || preg_match('/\.(?:js|xml|htm|html|css|jpg|png|svg|gif|htc|ico|zip|rar|pdf|mp3|swf|map|php)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    $_GET['K1LIB_URL'] = (strlen($_SERVER['REQUEST_URI']) > 0) ? substr($_SERVER['REQUEST_URI'], 1) : NULL;
    include __DIR__ . '/index.php';
}