<?php

use \Swoole\Process;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title('swoole-main');

$process = new Process(function (Process $p){
    $p->exec('/usr/bin/php',[__DIR__.'/run.php','name']);
},true,0,true);
$process->start();

while (true){
    $str = $process->read();
    echo $str;
}

Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
       // var_dump($ret);
    }
});














