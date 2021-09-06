<?php

use \Swoole\Process;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title('swoole-main');

for ($n = 1; $n <= 3; $n++) {
    $process = new Process(function () use ($n) {
        while (true){
            echo 'Child #' . getmypid() . " start and sleep {$n}s" . PHP_EOL;
            sleep($n);
            echo 'Child #' . getmypid() . ' exit' . PHP_EOL;

        }
    });
    $process->start();
}
/*
for ($n = 3; $n--;) {
    $status = Process::wait(true);
    echo "Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
}
echo 'Parent #' . getmypid() . ' exit' . PHP_EOL;
*/

Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
       // var_dump($ret);
    }
});














