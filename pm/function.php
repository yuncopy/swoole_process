<?php

use \Swoole\Process;

$processList = []; //主进程变量，管理进程变量
$worker = [];
/**
 * Notes: 创建进程
 * User: jackin.chen
 * Date: 2021/9/6 1:47 下午
 * function: init
 */
function init(){
    global $processList;
    $config = parse_ini_file('pm.conf',true);
    $child = $config['child'];
    foreach ($child as $name => $item){
        $params = explode(' ',$item);
        $process = new Process(function (Process $p) use($params){
            $p->exec($params[0],array_splice($params,1));
        });
        $pid = $process->start();//用户自定义进程
        $processList[$name] = [
            'pid' =>$pid,
            'date' =>date('Y-m-d H:i:s'),
            'status'=>[]
        ];
    }
    //删除进程
    rmProcess($child);

    //写入文件
    writeProcessData();
}


/**
 * Notes:m 写入文件状态
 * User: jackin.chen
 * Date: 2021/9/6 2:15 下午
 * function: writeProcessData
 */
function writeProcessData(){
    global $processList;
    $content = json_encode($processList,JSON_UNESCAPED_UNICODE);
    file_put_contents(__DIR__.'/p.data',$content);
}


/**
 * Notes: 更新进程状态
 * User: jackin.chen
 * Date: 2021/9/6 2:29 下午
 * function: setProcessStatus
 * @param $ret
 */
function setProcessStatus($ret){
    global $processList;
    foreach ($processList as $key => &$value){
        if($value['pid']==$ret['pid']){
            $value['status'] = $ret;
        }
    }
    writeProcessData();
}


/**
 * Notes: 监听文件变动
 * User: jackin.chen
 * Date: 2021/9/6 1:47 下午
 * function: watch
 */
function watch(){
    $child =  new Process(function($process){
        cli_set_process_title('swoole-watch');
        $file = __DIR__.'/pm.conf';
        $md5 = md5_file($file);
        while (true){
            $md5_check =  md5_file($file);
            if(strcmp($md5,$md5_check)){
                //子进程和父进程通信，发送信号
                $ppid = posix_getppid();
                $data = Process::kill($ppid,SIGUSR1); //向父进程发送信号 posix_getppid()
                var_dump($data);
                echo $ppid.'-文件被修改'.date('Y-m-d H:i:s').PHP_EOL;
                $md5 = $md5_check;
            }
            sleep(3);
        }
    });
    $child->start();
}


/**
 * Notes: Web 管理查看状态
 * User: jackin.chen
 * Date: 2021/9/6 1:46 下午
 * function: httpWeb
 */
function httpWeb(){
    $process = new Process(function (Process $p){
        $p->exec('/usr/bin/env',['php',__DIR__.'/http.php']);
    });
    $process->start();//用户自定义进程
}

/**
 * Notes: 删除文件
 * User: jackin.chen
 * Date: 2021/9/6 1:46 下午
 * function: rmProcess
 * @param $child
 */
function rmProcess($child){
    global $processList;
    $process = array_diff_key($processList,$child);
    foreach ($process as $pkey => $pvalue){
        Process::kill($pvalue['pid'],SIGTERM);
        unset($processList[$pkey]);
    }
}







