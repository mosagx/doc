<?php

class WebSocketServer
{
    public $log;
    public $event;
    public $signets;
    public $users;
    public $master;

    public function __construct($config = [])
    {
        if ('cli' !== substr(php_sapi_name(), 0, 3)) {
            die('请通过命令行模式运行!');
        }
        error_reporting(E_ALL);
        set_time_limit(0);
        ob_implicit_flush();
        $this->event = $config['event'];
        $this->log = $config['log'];
        $this->master = $this->WebSocket($config['address'], $config['port']);
        $this->sockets = array('s' => $this->master);
    }

    public function WebSocket($address, $port)
    {
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($server, $address, $port);
        socket_listen($server);
        $this->log('开始监听: '.$address.' : '.$port);

        return $server;
    }

    
}
