<?php
require_once '../config/config.php';
require_once '../lib/db.php';

date_default_timezone_set('Asia/Shanghai');

function exec_xhprof_disable(){
    $xhprof_data = xhprof_disable();
    $url = 'http://' . php_uname('n') . ':' .$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    if(!empty($_SERVER['QUERY_STRING'])) {
        $url .= '?' . urlencode($_SERVER['QUERY_STRING']);
    }

    $data = array(
        'url' => $url,
        'method' => $_SERVER['REQUEST_METHOD'],
        'time' => $_SERVER['REQUEST_TIME'],
        'xhprof_data' => $xhprof_data
    );
    global $config;
    global $redis_key;
    $Redis = getRedis($config);
    $Redis->zadd($redis_key, time(), serialize($data));
}

register_shutdown_function('exec_xhprof_disable');

xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);

function test() {
    for ($i = 0; $i < 4; $i++) {
        sleep(1);
    }
}
test();

