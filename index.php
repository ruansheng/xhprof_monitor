<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>xhprof monitor</title>
        <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
            *{
                margin:0px;
                padding:0px;
            }
            .title{
                height:60px;
                line-height:60px;
                font-size:24px;
                text-align:center;
            }
            tr,th{
                text-align:center;
            }
            .w-80{
                width:80px;
            }
            .w-150{
                width:150px;
            }
            .w-185{
                width:185px;
            }
            #xhprof-page{
                width:100%;
                height:100px;
            }

        </style>
    </head>
<body>
<?php
require_once dirname(__FILE__).'/config/config.php';
require_once dirname(__FILE__).'/lib/db.php';

date_default_timezone_set('Asia/Shanghai');

$pageCount = $page_count;

//计算偏移量
$page = $_GET['page'];
if(!is_numeric($page)) {
    $page = 1;
}

$start = ($page - 1) * $pageCount;
$end = $page * $pageCount - 1;

//查询数据
$Redis = getRedis($config);
$count = 0;
if($Redis) {
    $count = $Redis->zCard($redis_key);
    $rows = $Redis->zRevRange($redis_key, $start, $end);   //取最新的100个
}

//计算有多少页
$pageNum = ceil($count/$pageCount);

//格式化成数组
$list = array();
foreach($rows as $v) {
    $item = unserialize($v);

    if($item['method'] == 'GET'){
        $method_class = 'label-success';
    } else if($item['method'] == 'POST') {
        $method_class = 'label-primary';
    }

    $execute_time = sprintf('%.2f', $item['xhprof_data']['main()']['wt']/1000/1000);

    //计算时间 颜色
    $color = '#5cb85c';
    if($execute_time >= 1.0 && $execute_time < 2.0){
        $color = '#FFB5B5';
    } else if($execute_time >= 2.0 && $execute_time < 3.0) {
        $color = '#ff7575';
    } else if($execute_time >= 3.0) {
        $color = '#FF0000';
    }

    $list[] = array(
        'method_class' => $method_class,
        'url' => $item['url'],
        'method' => $item['method'],
        'time' => date('Y-m-d H:i:s', $item['time']),
        'execute_time' => $execute_time,
        'execute_time_color' => $color,
        'xhprof_data' => serialize($item['xhprof_data'])
    );
}
?>
<p class="bg-primary title">Api 执行效率监控</p>
<table class="table table-condensed">
    <tr>
        <th class="active w-185">请求时间</td>
        <th class="active w-80">请求方法</th>
        <th class="active">URL</th>
        <th class="active w-150">耗时(单位:秒)</th>
        <th class="active w-150">查看</th>
    </tr>
    <?php foreach($list as $v){ ?>
    <tr>
        <td class="active"><?php echo $v['time'];?></td>
        <td class="active"><span class="label <?php echo $method_class;?>"><?php echo $v['method'];?></span></td>
        <td class="active"><?php echo $v['url'];?></td>
        <td class="active" style="background:<?php echo $v['execute_time_color'];?>;"><?php echo $v['execute_time'];?></td>
        <td class="active"><a href="javascript:void(0);" xhprof-graph='<?php echo $v["xhprof_data"];?>'>图表</a></td>
    </tr>
    <?php } ?>
</table>
<nav id="xhprof-page">
    <ul class="pagination">
        <?php if($count > 0){?>
            <li id="previous">
                <a href="" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php }?>
        <?php for($i = 1; $i <= $pageNum; $i++){?>
                <?php if($i == $page){?>
                    <li class="active"><a href="./index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
                <?php }else{?>
                    <li><a href="./index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
                <?php }?>
        <?php }?>
        <?php if($count > 0){?>
            <li id="next">
                <a href="" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php }?>
    </ul>
</nav>
<form action="./xhprof/view-graph.php" id="form-submit" method="post" target="_blank">
    <input type="hidden" name="log" id="xhprof-data" value=""/>
</form>
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="./bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(function(){
        //查看图表
        $('[xhprof-graph]').click(function(){
            var xhprof_data = $(this).attr('xhprof-graph');
            if(xhprof_data != '') {
                $('#xhprof-data').val(xhprof_data);
                $('#form-submit').submit();
            }
        });

        //翻页 前一页
        $('#previous').click(function(){
            var href = $('.pagination').children('[class="active"]').prev('li').children('a').attr('href');
            $(this).children('a').attr('href', href);
        });

        //翻页 后一页
        $('#next').click(function(){
            var href = $('.pagination').children('[class="active"]').next('li').children('a').attr('href');
            $(this).children('a').attr('href', href);
        });
    });
</script>
</body>
</html>