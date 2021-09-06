<?php

cli_set_process_title('swoole-exec');
while (true){
    echo date('Y-m-d H:i:s').PHP_EOL;
    sleep(3);
}