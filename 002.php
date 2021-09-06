<?php
use \Swoole\Process;
echo '当前进程ID：'.posix_getpid().PHP_EOL;
cli_set_process_title('swoole-main');
$child =  new Process(function() {
   $file = __DIR__.'/db.conf';
   $md5 = md5_file($file);
   while (true){
       $md5_check =  md5_file($file);
       if(strcmp($md5,$md5_check)){
           echo '文件被修改'.date('Y-m-d H:i:s').PHP_EOL;
       }
       sleep(1);
   }
});
$child->start();

echo '当前进程名称：'.cli_get_process_title().PHP_EOL;
Process::wait();

/*
Process::signal(SIGCHLD, function($sig) {
    //必须为false，非阻塞模式
    while($ret =  Process::wait(false)) {
        var_dump($ret);
    }
});
?












