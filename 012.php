<?php

// 4.3.X 版本没有问题
cli_set_process_title("master");
$manager = new Swoole\Process(function (Swoole\Process $p) {
    cli_set_process_title("manager");
    while(true) {
        echo "a-".date('Y-m-d H:i:s').PHP_EOL;
        sleep(1);
    }
});
$manager->start();

$manager1 = new Swoole\Process(function(Swoole\Process $worker){
    cli_set_process_title("manager1");
    $i=0;
    while($i < 20) {
        echo "b-".date('Y-m-d H:i:s').PHP_EOL;
        sleep(1);
        ++$i;
    }
    $ppid = posix_getppid();
    var_dump(1,$ppid);
    Swoole\Process::kill($ppid,SIGUSR1);
});
$manager1->start();

//和定时器实现异步监听信号
Swoole\Process::signal(SIGUSR1, function ($signo){
    var_dump($signo);
    echo "c-".date('Y-m-d H:i:s').PHP_EOL;
});

//CTL+c 或者 kill -SIGINT PID
Swoole\Process::signal(SIGINT, function ($signo){
    echo "d-".date('Y-m-d H:i:s').PHP_EOL;
    while ($ret = Swoole\Process::wait(false)){
        var_dump($ret);
    }
    exit();
});

//添加一个 tick 定时器，信号监听
Swoole\Timer::tick(5000,function (){
    echo "d-".date('Y-m-d H:i:s').PHP_EOL;
});





