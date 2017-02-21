<?php


use Wqy\WechatQy\Handler;

umask(0);


require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$h = new Handler($config, $_REQUEST, $_SESSION);

try {
    $h->handle();
}
catch (Exception $e)
{
    echo $e->getMessage();
}
