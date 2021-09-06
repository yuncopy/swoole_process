<?php
use \Swoole\Process;
use \Swoole\Coroutine\MySQL;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title('swoole-main');

/**
CREATE TABLE `zerg`.`swoole_order`  (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '表ID',
`order_no` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
`is_pay` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否支付',
`is_notice` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否通知',
PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;
 */
$child =  new Process(function() {
    $swoole_mysql = new MySQL();
    $swoole_mysql->connect([
        'host' => '192.168.33.10',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'zerg',
    ]);
    while (true){
        try{
            $test = 'insert into swoole_order (order_no) value ("'.md5(time()).'")';
            $swoole_mysql->query($test);

            var_dump($swoole_mysql);

        }catch (Exception $exception){
            echo $exception->getMessage().PHP_EOL;
        }
       sleep(1);
    }
},false,0,true);
$child->start();
echo '当前进程名称：'.cli_get_process_title().PHP_EOL;

Process::wait();
/*
Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
        var_dump($ret);
    }
});
*/













