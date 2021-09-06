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
    while (true){
        try{
            $order = "select id,order_no,is_pay,is_notice from swoole_order where is_pay=1 and is_notice=0 limit 1";
            $res = $swoole_mysql->query($order);
            if($res && count($res)){
                $process->write($res[0]['order_no']);
            }
        }catch (Exception $exception){
            echo $exception->getMessage().PHP_EOL;
        }

       sleep(3);
    }
},false,1,true); //1：创建SOCK_STREAM类型管道
$child_select->start();

//该子进程负责对已支持订单发送通知
$child_notice =  new Process(function(Process $process) {
    while (true){
        $order_notice = $process->read();
        if($order_notice){
            echo '进程2获取到订单号:'.$order_notice.PHP_EOL;
        }
        usleep(0.5 *1000 * 1000); //微秒
    }

},false,1,true); //1：创建SOCK_STREAM类型管道
$child_notice->start();

//主进程接收子进程的消息后往另一个子进程写消息
while (true){
    $order_no = $child_select->read();
    if($order_no){
        $child_notice->write($order_no);
    }
    usleep(0.5 *1000 * 1000); //微秒
}

echo '当前进程名称：'.cli_get_process_title().PHP_EOL;

Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
        var_dump($ret);
    }
});













