<?php
//move.php
cli_set_process_title('swoole-move');
$i = 0;
while (true){
    echo 'move-data'.PHP_EOL;
    sleep(3);
    if($i > 3){
        break;
    }
    ++$i;
}
