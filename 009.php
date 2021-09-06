<?php

use \Swoole\Process;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title("master");
$manager=new Process(function(Process $process){
    cli_set_process_title("manager");
    while(true) {
        echo "a-".date('Y-m-d H:i:s').PHP_EOL;
        sleep(1);
    }
});
$manager->start();

$manager1=new Process(function(Process $worker){
    cli_set_process_title("manager1");
    $i=0;
    while($i < 10) {
        echo "b-".date('Y-m-d H:i:s').PHP_EOL;
        sleep(1);
        ++$i;
    }
    Process::kill($worker->pid, SIGCHLD);
    var_dump(1,$worker->pid);
    $worker->exit(); // 退出子进程
});
$pid = $manager1->start();
var_dump(2,$pid);

$sig = Process::signal(SIGCHLD,function (){
    while ($ret = swoole_process::wait(false)) {
       var_dump(5,$ret);
    }
    file_put_contents('process.log', '222', FILE_APPEND); // todo: 这句话没有执行
});
var_dump(3,$sig);
while ($ret =  Process::wait(true)){
    var_dump(4,$ret);
}









