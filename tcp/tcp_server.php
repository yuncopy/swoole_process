<?php
class Server{
    private $serv;
    public function __construct(){

        $this->serv = new swoole_server("0.0.0.0",9501);
        $this->serv->set(
            array(
                'worker_num' => 1,                //一般设置为服务器CPU数的1-4倍
                'daemonize' => 1,                 //以守护进程执行
                'max_request' => 10000,
                'dispatch_mode' => 2,
                'task_worker_num' => 8,           //task进程的数量
                "task_ipc_mode " => 3,            //使用消息队列通信，并设置为争抢模式
                "log_file" => "task.log",
            )
        );
        $this->serv->on('Receive',array($this,'onReceive'));
        $this->serv->on('Task',array($this,'onTask'));
        $this->serv->on('Finish',array($this,'onFinish'));
        $this->serv->start();

    }
    public function onReceive(swoole_server $serv, $fd, $from_id, $data){
        $serv->task($data);
    }
    public function onTask($serv, $task_id, $from_id, $data){
        $data = json_decode($data,true);
        if(!empty($data)){
            return $this->sendsms($data['mobile'],$data['message']);
        }
    }
    public function onFinish($serv, $task_id, $data){
        echo "Task {$task_id} finish\n";
    }
    public function sendsms($mobile,$text)
    {

        sleep(rand(1,3));

        /*
        $timestamp = date("Y-m-d H-i-s");
        $pid = "888888888";
        $send_sign = md5($pid.$timestamp."abcdefghijklmnopqrstuvwxyz");
        $post_data = array();
        $post_data['partner_id'] = $pid;
        $post_data['timestamp'] =$timestamp;
        $post_data['mobile'] = $mobile;
        $post_data['message'] = $text;
        $post_data['sign'] = $send_sign;
        $url='http://182.92.149.100/sendsms';
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);

        //为了支持cookie
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if(strpos($result,"success")!==false)
        {
            $outstr=1;
        }
        else
        {
            $outstr=502;
        }
        return $outstr;
        */


    }
}
$server = new Server();
?>