<?php
require_once '../config/config.php';
require_once '../lib/db.php';

function exec_xhprof_disable(){
    $xhprof_data = xhprof_disable();
    $url = $_SERVER['REQUEST_URI']. '?' . urlencode($_SERVER['QUERY_STRING']);
    $data = array(
        'url' => $url,
        'host' => php_uname('n'),
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

//echo "执行时长:".sprintf('%.2f',$xhprof_data['main()']['wt']/1000/1000)."<br/>";
//echo "<a target='_blank' href='./view-graph.php?log=".htmlspecialchars(serialize($xhprof_data))."'>查看图表</a><br/>";
