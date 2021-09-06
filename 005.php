<?php
use \Swoole\Process;
use \Swoole\Coroutine\MySQL;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title('swoole-main');

//该子进程负责查询已支付并未通知订单信息
$child_select =  new Process(function(Process $process) {
    $swoole_mysql = new MySQL();
    $swoole_mysql->connect([
        'host' => '192.168.33.10',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'zerg',
    ]);
    $offset = 0;
    while (true){
        try{
            $order = "select order_no from swoole_order where is_pay=1 and is_notice=0 limit {$offset},1";
            $res = $swoole_mysql->query($order);
            if($res && count($res)){
                $process->push($res[0]['order_no']);
            }
        }catch (Exception $exception){
            echo $exception->getMessage().PHP_EOL;
        }
        $offset++;
       sleep(3);
    }
},false,1,true); //1：创建SOCK_STREAM类型管道
$child_select->useQueue(2);
$child_select->start();

//该子进程负责对已支持订单发送通知
$child_notice1 =  new Process(function(Process $process) {
    while (true){
        $order_notice = $process->pop();
        if($order_notice){
            echo '1子进程从消息队列取消息:'.$order_notice.PHP_EOL;
        }
        usleep(0.5 *1000 * 1000); //微秒
    }

},false,1,true); //1：创建SOCK_STREAM类型管道
$child_notice1->useQueue(2);
$child_notice1->start();


//该子进程负责对已支持订单发送通知
$child_notice2 =  new Process(function(Process $process) {
    while (true){
        $order_notice = $process->pop();
        if($order_notice){
            echo '2子进程从消息队列取消息:'.$order_notice.PHP_EOL;
        }
        usleep(0.5 *1000 * 1000); //微秒
    }

},false,1,true); //1：创建SOCK_STREAM类型管道
$child_notice2->useQueue(2);
$child_notice2->start();

/**
 * 思路：
 * 1、进程之间消息不会发生同时争夺
 * 2、如果发送通知比较耗时，没有处理完又从数据库中读取消息 -> 考虑使用redis锁实现
 */

echo '当前进程名称：'.cli_get_process_title().PHP_EOL;

//Process::wait();

for ($n = 3; $n--;) {
    $status = Process::wait(true);
    echo "Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
}

/*
Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
        var_dump($ret);
    }
});
*/














