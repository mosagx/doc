<?php

class WebSocketService
{
    private $address = '127.0.0.1';
    private $port = 9999;
    private $_sockets;

    public function __construct($address, $port)
    {
        $this->address = $address;
        $this->port = $port;
    }

    public function service()
    {
        $tcp  = getprotobyname('tcp');
        $sock = socket_create(AF_INET, SOCK_STREAM, $tcp);
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
        if ($sock < 0) {
            throw new Exception('failed to create socket: '.socket_strerror($sock)."\n");
        }
        socket_bind($sock, $this->address, $this->port);
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
                    $this->handShaking($newClient, $line);
                    //获取client ip
                    socket_getpeername($newClient, $ip);
                    $clients[$ip] = $newClient;
                    echo "Client ip:{$ip}  \n";
                    echo "Client msg:{$line} \n";
                } else {
                    socket_recv($_sock, $buffer, 2048, 0);
                    $msg = $this->message($buffer);
                    echo "{$key} clinet msg:",$msg,"\n";
                    fwrite(STDOUT, 'Please input a argument:');
                    $response = trim(fgets(STDIN));
                    $this->send($_sock, $response);
                    echo "{$key} response to Client:".$response,"\n";
                }
            }
        }
    }

    public function handShaking($newClient, $line)
    {
        $headers = array();
        $lines   = preg_split("/\r\n/", $line);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n".
        "Upgrade: websocket\r\n".
        "Connection: Upgrade\r\n".
        "WebSocket-Origin: $this->address\r\n".
        "WebSocket-Location: ws://$this->address:$this->port/websocket/websocket\r\n".
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

        return socket_write($newClient, $upgrade, strlen($upgrade));
    }

    public function message()
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        if (126 === $len) {
            $masks = substr($buffer, 4, 4);
            $data  = substr($buffer, 8);
        } elseif (127 === $len) {
            $masks = substr($buffer, 10, 4);
            $data  = substr($buffer, 14);
        } else {
            $masks = substr($buffer, 2, 4);
            $data  = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); ++$index) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }

        return $decoded;
    }

    public function send($newClinet, $msg)
    {
        $msg = $this->frame($msg);
        socket_write($newClinet, $msg, strlen($msg));
    }

    public function frame($msg)
    {
        $a = str_split($msg, 125);
        if (1 == count($a)) {
            return "\x81".chr(strlen($a[0])).$a[0];
        }
        $ns = '';
        foreach ($a as $o) {
            $ns .= "\x81".chr(strlen($o)).$o;
        }

        return $ns;
    }

    public function close()
    {
        return socket_close($this->_sockets);
    }
}

$sock = new SocketService();
$sock->run();
