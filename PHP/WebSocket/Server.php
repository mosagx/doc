<?php

include 'WebSocketServer.php';

$config = [
    'address' => '127.0.0.1',
    'port' => '8000',
    'event' => 'WSevent',
    'log' => true,
];

$websocket = new WebSocketServer($config);
$websocket->run();
function WSevent($type, $event)
{
    global $websocket;
    if ('in' == $type) {
        $websocket->log('Client on line id:'.$event['k']);
    } elseif ('out' == $type) {
        $websocket->log('Client exit id:'.$event['k']);
    } elseif ('msg' == $type) {
        $websocket->log($event['k'].' message:'.$event['msg']);
        roboot($event['sign'], $event['msg']);
    }
}

function roboot($sign, $t)
{
    global $websocket;
    switch ($t) {
        case 'hello':
            $show = 'hello,GIt @ OSC';
            break;
        case 'name':
            $show = 'Robot';
            break;
        case 'time':
            $show = 'time now:'.date('Y-m-d H:i:s');
            break;
        case 'bye':
            $show = '( ^_^ )/~~bye!';
            $websocket->write($sign, 'Robot:'.$show);
            $websocket->close($sign);

            return;
            break;
        case 'rand':
            $array = ['origin', 'apple', 'banana'];
            $show = $array[rand(0, 2)];
            break;
        default:
            $show = '( ⊙o⊙?)unknow! you can try: hello,name,time,bye,rand.';
    }
    $websocket->write($sign, 'Robot:'.$show);
}
