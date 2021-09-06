<?php

/**
 * 1、通过文件共享
 * 2、Redis共享
 * 3、Table共享
 *
 *
 */



//使用文件共享进程状态
cli_set_process_title('swoole-http');
$http = new Swoole\Http\Server('0.0.0.0', 9501);
$http->set([
    'worker_num'    => 1
]);
$http->on('Request', function ($request, $response) {
    $response->header('Content-Type', 'application/json; charset=utf-8');
    $content = file_get_contents(__DIR__.'/p.data');
    $response->end($content);
});
$http->start();