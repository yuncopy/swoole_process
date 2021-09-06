<?php

use Swoole\Process;
echo "当前进程ID:".posix_getpid().PHP_EOL; //获取进程ID;

cli_set_process_title("mymain"); // 设置了进程名称
$child1 = new Process(function (){
    echo "当前子进程1的ID:".posix_getpid().PHP_EOL; //获取进程ID;
    //设置了进程名称
    // cli_set_process_title();
    while(true){ //写个死循环,不让进程退出
        sleep(1);
        echo date('Y-m-d H:i:s').PHP_EOL;
    }
});

$child1->start();
$child2 = new Process(function (){
    echo "当前子进程2的ID:".posix_getpid().PHP_EOL; //获取进程ID;
    //设置了进程名称
    // cli_set_process_title();
    $i=0;
    while ($i < 10){
        sleep(1);
        echo date('Y-m-d H:i:s').PHP_EOL;
        ++$i;
    }
});
$child2->start();
// 原始的方法 否则在linux下会出现ppid=0 的孤儿进程
// mac 下会强制设置为ppid=1 整个系统的根节点的进程

while($ret = Process::wait(true)){
    echo "PID={$ret['pid']} \n";
    var_dump($ret);
}








