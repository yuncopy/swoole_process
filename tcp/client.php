<?php
class Client{
    public $client;
    public function __construct(){
        $this->client= new swoole_client(SWOOLE_SOCK_TCP);//默认同步tcp客户端，添加参数SWOOLE_SOCK_ASYNC为异步
    }
    public function connect(){
        if(!$this->client->connect('127.0.0.1',9501,1)){
            throw new Exception(sprintf('Swoole Error: %s', $this->client->errCode));
        }
    }
    public function send($data){
        if($this->client->isConnected()){
            $data = json_encode($data);
            //print $data;
            if($this->client->send($data)){
                return 1;
            }else{
                throw new Exception(sprintf('Swoole Error: %s', $this->client->errCode));
            }
        }else{
            throw new Exception('Swoole Server does not connected.');
        }

    }
    public function close(){
        $this->client->close();
    }
}
$client= new Client();
$client->connect();
$data=array(
    'mobile'=>'18511487955',
    'message'=>'you mobile 18511487955'
);
if($client->send($data)){
    echo 'succ';
}else{
    echo 'fail';
}
