<?php

/**
 * 获取redis实例
 * @param $config
 * @return mixed
 */
function getRedis($config) {
    $Redis = new Redis();
    $flag = $Redis->connect($config['host'], $config['port']);
    if($flag) {
        if(isset($config['auth']) && !empty($config['auth'])) {
            $ret = $Redis->auth($config['auth']);
            if($ret) {
                return $Redis;
            } else {
                return false;
            }
        } else {
            return $Redis;
        }
    } else {
        return false;
    }

}
