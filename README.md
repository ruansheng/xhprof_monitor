#xhprof_monitor

#notice
请确保php环境已经安装 redis扩展、xhprof扩展

#download
[下载xhprof monitor](https://github.com/ruansheng/xhprof_monitor/archive/master.zip)

# modify config.php
修改配置文件
```php
// /config/config.php
//redis 地址配置
$config = array(
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => ''
);

$redis_key = 'ququ_api_xhprof_data';    //redis zset key

$page_count = 10;    //每页显示的条数
```

# deploy
将项目部署到支持php环境的web服务器中
访问http://host/index.php

如果访问报错如下:
```Bash
failed to execute cmd: " dot -Tpng". stderr: `sh: 1: dot: not found '
```
解决方案:
```Bash
mac : brew install graphviz
centos : yum install graphviz
ubuntu : apt-get install graphviz
```

#show
浏览器访问显示如下
![](https://github.com/ruansheng/xhprof_monitor/raw/master/image/index.png)

可点击图表查看执行流程图像
![](https://github.com/ruansheng/xhprof_monitor/raw/master/image/graph.png)
