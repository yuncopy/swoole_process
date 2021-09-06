<?php
//https://www.php.cn/php-weizijiaocheng-370397.html
// declare 作用
declare(ticks = 1);
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
    var_dump($ppid);
    posix_kill($ppid,SIGUSR1);
});
$manager1->start();

pcntl_signal(SIGUSR1, function ($signo){
    var_dump(1,$signo);
    echo "c-".date('Y-m-d H:i:s').PHP_EOL;
});

//从4.4版本开始底层将不再将信号监听作为 EventLoop Exit 的 block 条件
//https://github.com/swoole/swoole-src/issues/2731
//同步阻塞方式
while ($ret =  Swoole\Process::wait(true)){
    var_dump(4,$ret);
}



