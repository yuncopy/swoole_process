<?php

// 4.3.X 版本没有问题
for ($i = 0; $i < 5; $i++) {
    $p = new Swoole\Process(function (Swoole\Process $p) {
        sleep(rand(2, 6));
    });

    $p->start();
}

Swoole\Process::signal(SIGCHLD, function ($signo) {
    echo "\n $signo \n";

    while ($ret = Swoole\Process::wait(false)) {
        print_r($ret);
    }
});

