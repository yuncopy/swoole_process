<?php
//pm.php
use \Swoole\Process;
$pid = posix_getpid();
echo '当前进程ID：'.$pid.PHP_EOL;
//file_put_contents('pid.log',$pid);
cli_set_process_title('swoole-main');

//包含公共函数
require  'function.php';

//监听进程
watch();

//http进程
httpWeb();

//启动进程
init();

//主进程拦截子进程发送信号
Process::signal(SIGUSR1, function($sig) {
    echo '进程正在重新配置'.date('Y-m-d H:i:s').'-'.$sig.PHP_EOL;
    init();
});


Swoole\Timer::tick(2000,function (){
    echo "d-".date('Y-m-d H:i:s').PHP_EOL;
});

/**
 * ctrl + C 或者 kill 杀死进程
 */
Swoole\Process::signal(SIGINT, function ($signo){
    echo "e-".date('Y-m-d H:i:s').'-'.$signo.PHP_EOL;
    while ($ret = Swoole\Process::wait(false)){
        var_dump($ret);
    }
    exit();
});


/**
 * 子进程自动退出
 */
Swoole\Process::signal(SIGCHLD, function ($signo){
    while ($ret = Swoole\Process::wait(false)){
        setProcessStatus($ret);
    }
});




//从4.4版本开始底层将不再将信号监听作为 EventLoop Exit 的 block 条件
// https://github.com/swoole/swoole-src/issues/2731
//在异步信号回调中执行wait
//查看linux下的信号列表 kill -l
//查看版本 php --ri swoole

/*
Process::signal(SIGCHLD, function() {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
        //执行回收后的处理逻辑，比如拉起一个新的进程
        var_dump($ret);
    }
});
*/





