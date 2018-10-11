<?php

class SocketService
{
    private $address;

    private $port;

    private $_sockets;

    public function __construct($address = '127.0.0.1', $port = 8484)
    {
        if ('cli' !== substr(php_sapi_name(), 0, 3)) {
            die('please run this to command terimal!');
        }
        $this->address = $address;
        $this->port    = $port;
    }

    public function service()
    {
        //获取tcp协议号码。
        $tcp  = getprotobyname('tcp');
        $sock = socket_create(AF_INET, SOCK_STREAM, $tcp);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);

        // 异常
        if ($sock < 0) {
            throw new Exception('failed to create socket: '.socket_strerror($sock)."\n");
        }

        // 绑定
        socket_bind($sock, $this->address, $this->port);

        // 监听
        socket_listen($sock, $this->port);
        echo "listen on $this->address $this->port ... \n";

        $this->_sockets = $sock;
    }

    public function run()
    {
        $this->service();
        $clients[] = $this->_sockets;
        while (true) {
            $changes = $clients;
            $write   = null;
            $except  = null;
            socket_select($changes, $write, $except, null);
            foreach ($changes as $key => $_sock) {
                if ($this->_sockets == $_sock) { //判断是不是新接入的socket
                    if (false === ($newClient = socket_accept($_sock))) {
                        die('failed to accept socket: '.socket_strerror($_sock)."\n");
                    }
                    $line = trim(socket_read($newClient, 1024));
                    $this->handshaking($newClient, $line);

                    //获取client ip
                    socket_getpeername($newClient, $ip);
                    $clients[$ip] = $newClient;
                    echo "Client ip:{$ip}  \n";
                    echo "Client msg:{$line} \n";
                } else {

                    $bytes = socket_recv($_sock, $buffer, 2048, 0);

                    if (false === $bytes) {
                      echo "socket_recv() failed; reason: " . socket_strerror(socket_last_error($socket)) . "\n";
                    }

                    $this->log($bytes);

                    // 获取消息
                    $msg = $this->message($buffer);

                    // 截取msg
                    $msg = substr($msg, 4);

                    $this->log($msg);

                    $this->roboot($_sock, $msg);

                    // $this->log($_sock);

                    // // 业务代码（发送消息示例）
                    // echo "{$key} clinet msg:",$msg,"\n";
                    // fwrite(STDOUT, 'Please input a argument:');
                    // $response = trim(fgets(STDIN));
                    // $this->send($_sock, $response);
                    // echo "{$key} response to Client:".$response,"\n";
                }
            }
        }
    }

    /**
     * 机器人示例
     */
    public function roboot($_sock, $msg)
    {
        switch ($msg) {
            case 'hello':
                $show = 'Hello what are you doing now?';
                break;
            case 'name':
                $show = 'My name is LeleJun!';
                break;
            case 'time':
                $show = 'Time now:'.date('Y-m-d H:i:s');
                break;
            case 'bye':
                $show = '( ^_^ )/~~bye!';
                socket_close($_sock);
                return;
                break;
            case 'rand':
                $array = ['origin', 'apple', 'banana'];
                $show = $array[rand(0, 2)];
                break;
            default:
                $show = '( ⊙o⊙?)unknow! you can try: hello,name,time,bye,rand.';
        }
        $this->send($_sock, $show);
    }

    /**
     * 握手处理.
     *
     * @param $newClient socket
     *
     * @return int 接收到的信息
     */
    public function handshaking($newClient, $line)
    {
        $headers = [];
        $lines   = preg_split("/\r\n/", $line);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey    = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade   = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n".
          "Upgrade: websocket\r\n".
          "Connection: Upgrade\r\n".
          "WebSocket-Origin: $this->address\r\n".
          "WebSocket-Location: ws://$this->address:$this->port/websocket/websocket\r\n".
          "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

        return socket_write($newClient, $upgrade, strlen($upgrade));
    }

    /**
     * 解析接收数据.
     *
     * @param $buffer
     *
     * @return null|string
     */
    public function message($buffer)
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        if (126 === $len) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } elseif (127 === $len) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); ++$index) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }

        return $decoded;
    }

    /**
     * 发送数据.
     *
     * @param $newClinet 新接入的socket
     * @param $msg  要发送的数据
     *
     * @return int|string
     */
    public function send($newClinet, $msg)
    {
        $msg = $this->frame($msg);
        socket_write($newClinet, $msg, strlen($msg));
    }

    public function frame($s)
    {
        $a = str_split($s, 125);
        if (1 == count($a)) {
            return "\x81".chr(strlen($a[0])).$a[0];
        }
        $ns = '';
        foreach ($a as $o) {
            $ns .= "\x81".chr(strlen($o)).$o;
        }

        return $ns;
    }

    /**
     * 关闭socket.
     */
    public function close()
    {
        return socket_close($this->_sockets);
    }

    function getKey($req) {
      $key = null;
      if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $req, $match)) { 
          $key = $match[1]; 
      }
      return $key;
    }

    protected function log($str = '')
    {
      file_put_contents('./Log/'.date('Y-m-d').'.log', "[".date('Y-m-d H:i:s')."] " . $str ."\r\n", FILE_APPEND);
    }
}

$sock = new SocketService();
$sock->run();
