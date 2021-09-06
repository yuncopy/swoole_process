<?php
use \Swoole\Process;
use \Swoole\Coroutine\MySQL;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title('swoole-main');
$child =  new Process(function() {
    $swoole_mysql = new MySQL();
    $swoole_mysql->connect([
        'host' => '192.168.33.10',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'information_schema',
    ]);
    $checkConnect = "select 1";
    $checkProcessCount = "select count(*) as c from information_schema.processlist";
    $checkThread = "select * from information_schema.GLOBAL_STATUS where VARIABLE_NAME like 'Thread%'";
    //$res = $swoole_mysql->query('select 1'); //测试
    while (true){
        $checkResult[] = date('Y-m-d H:i:s');
        try{
            $swoole_mysql->query($checkConnect);
            $checkResult[] = '检查连接正常';
            $res = $swoole_mysql->query($checkProcessCount);
            $checkResult[] = '当前连接数'.$res[0]['c'];
            $res = $swoole_mysql->query($checkThread);
            $checkResult[] = '检查线程情况';
            foreach ($res as $row){
                foreach ($row as $key => $value){
                    $checkResult[] = $key.':'.$value;
                }
            }
            $checkResult[] = '---------------------';
            echo implode(PHP_EOL,$checkResult);
        }catch (Exception $exception){
            echo $exception->getMessage().PHP_EOL;
        }
       sleep(5);
    }
},false,1,true);
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












