<?php
/**
 * 
 * @author ruansheng
 * @since  2015-07-03
 */

//redis 地址配置
$config = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => ''
);

$redis_key = 'ququ_api_xhprof_data';    //redis zset key

$page_count = 2;    //每页显示的条数