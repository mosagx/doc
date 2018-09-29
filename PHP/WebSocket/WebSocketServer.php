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
            die('please run this to command terimal!');
        }
        error_reporting(E_ALL);
        set_time_limit(0);
        ob_implicit_flush();
        $this->event   = $config['event'];
        $this->log     = $config['log'];
        $this->master  = $this->WebSocket($config['address'], $config['port']);
        $this->sockets = ['s' => $this->master];
    }

    /**
     * 服务进程挂起
     */
    public function WebSocket($address, $port)
    {
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($server, $address, $port);
        socket_listen($server);
        $this->log('start listen: '.$address.' : '.$port);

        return $server;
    }

    /**
     * 监听socket
     */
    public function run()
    {
        // 死循环，直到socket断开
        while (true) {
            $changes = $this->sockets;
            $write   = null;
            $except  = null;
            @socket_select($changes, $write, $except, null);
            foreach ($changes as $sign) {
                // 如果有新的client连接进来
                if ($sign == $this->master) {

                    // 接收一个socket连接
                    $client = socket_accept($this->master);

                    // 将新连接进来的socket存进连接池
                    $this->sockets[] = $client;
                    $user = [
                        'socket' => $client,    // 记录新连接进来client的socket信息
                        'hand' => false,        // 标志该socket资源没有完成握手
                    ];
                    $this->users[] = $user;

                    // 根据socket在user池里面查找相应的$k,即健ID
                    $k = $this->search($client);
                    $eventreturn = ['k' => $k, 'sign' => $sign];
                    $this->eventoutput('in', $eventreturn);
                } else {

                    // 读取该socket的信息，注意：第二个参数是引用传参即接收数据，第三个参数是接收数据的长度
                    $len  = socket_recv($sign, $buffer, 2048, 0);
                    $k    = $this->search($sign);
                    $user = $this->users[$k];
                    
                    // 如果接收的信息长度小于7，则该client的socket为断开连接
                    if ($len < 7) {
                        $this->close($sign);
                        $eventreturn = ['k' => $k, 'sign' => $sign];
                        $this->eventoutput('out', $eventreturn);
                        continue;
                    }

                    // 判断该socket是否已经握手
                    if (!$this->users[$k]['hand']) {

                        // 没有握手进行握手
                        $this->handshake($k, $buffer);
                    } else {

                        // 对接受到的信息进行uncode处理
                        $buffer = $this->uncode($buffer);
                        $eventreturn = ['k' => $k, 'sign' => $sign, 'msg' => $buffer];
                        $this->eventoutput('msg', $eventreturn);
                    }
                }
            }
        }
    }

    /**
     * 通过标示遍历获取id
     */
    public function search($sign)
    {
        foreach ($this->users as $k => $v) {
            if ($sign == $v['socket']) {
                return $k;
            }
        }

        return false;
    }

    /**
     * 通过标示断开连接
     */
    public function close($sign)
    {
        $k = array_search($sign, $this->sockets);
        socket_close($sign);
        unset($this->sockets[$k]);
        unset($this->users[$k]);
    }

    /**
     * 握手连接
     */
    public function handshake($k, $buffer)
    {
        $buf     = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);
        $key     = trim(substr($buf, 0, strpos($buf, "\r\n")));
        $new_key = base64_encode(sha1($key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
        $new_message .= "Upgrade: websocket\r\n";
        $new_message .= "Sec-WebSocket-Version: 13\r\n";
        $new_message .= "Connection: Upgrade\r\n";
        $new_message .= 'Sec-WebSocket-Accept: '.$new_key."\r\n\r\n";
        socket_write($this->users[$k]['socket'], $new_message, strlen($new_message));
        $this->users[$k]['hand'] = true;

        return true;
    }

    public function uncode($str)
    {
        $mask = [];
        $data = '';
        $msg  = unpack('H*', $str);
        $head = substr($msg[1], 0, 2);
        if (8 === hexdec($head[1])) {
            $data = false;
        } elseif (1 === hexdec($head[1])) {
            $mask[] = hexdec(substr($msg[1], 4, 2));
            $mask[] = hexdec(substr($msg[1], 6, 2));
            $mask[] = hexdec(substr($msg[1], 8, 2));
            $mask[] = hexdec(substr($msg[1], 10, 2));
            $s = 12;
            $e = strlen($msg[1]) - 2;
            $n = 0;
            for ($i = $s; $i <= $e; $i += 2) {
                $data .= chr($mask[$n % 4] ^ hexdec(substr($msg[1], $i, 2)));
                ++$n;
            }
        }

        return $data;
    }

    public function code($msg)
    {
        $msg = preg_replace(['/\r$/', '/\n$/', '/\r\n$/'], '', $msg);
        $frame = [];
        $frame[0] = '81';
        $len = strlen($msg);
        $frame[1] = $len < 16 ? '0'.dechex($len) : dechex($len);
        $frame[2] = $this->ord_hex($msg);
        $data = implode('', $frame);

        return pack('H*', $data);
    }

    public function ord_hex($data)
    {
        $msg = '';
        $l = strlen($data);
        for ($i = 0; $i < $l; ++$i) {
            $msg .= dechex(ord($data[$i]));
        }

        return $msg;
    }

    /**
     * 通过id推送信息
     */
    public function idwrite($id, $t)
    {
        if (!$this->users[$id]['socket']) {
            return false;
        }
        //没有这个标示
        $t = $this->code($t);

        return socket_write($this->users[$id]['socket'], $t, strlen($t));
    }

    /**
     * 通过标示推送信息
     */
    public function write($k, $t)
    {
        $t = $this->code($t);

        return socket_write($k, $t, strlen($t));
    }

    /**
     * 事件回调
     */
    public function eventoutput($type, $event)
    {
        call_user_func($this->event, $type, $event);
    }

    /**
     * 控制台输出
     */
    public function log($t)
    {  
        if ($this->log) {
            $t = $t."\r\n";
            fwrite(STDOUT, iconv('utf-8', 'gbk//IGNORE', $t));
        }
    }

}
