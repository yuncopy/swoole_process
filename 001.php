<?php
use \Swoole\Process;
echo '当前进程ID：'.posix_getpid().PHP_EOL;

cli_set_process_title('swoole-main');

$child =  new Process(function() {
    $http = new Swoole\Http\Server("0.0.0.0", 9501);
    $http->set([
        "worker_num"=>4
    ]);
    $http->on('request', function ($request, $response) {
        $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
    });
    $http->on('start', function ( $serv) {
        cli_set_process_title('swoole-master');
    });
    $http->on('managerstart', function ($serv) {
        cli_set_process_title('swoole-manager');
    });

    $http->on('workerstart', function ($serv, $worker_id) {
        cli_set_process_title('swoole-worker');
    });

    $http->start();
});

$child->start();

echo '当前进程名称：'.cli_get_process_title().PHP_EOL;

Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
        var_dump($ret);
    }
});











