<?php

class WxLogin
{
    private $username;

    private $pwd;

    private $index;

    private $_apis = [
        'home'        => 'https://mp.weixin.qq.com',
        'start_login' => 'https://mp.weixin.qq.com/cgi-bin/bizlogin?action=startlogin',
        'qrcode'      => 'https://mp.weixin.qq.com/cgi-bin/loginqrcode?action=getqrcode&param=4300&rd=120',
        'login_ask'   => 'https://mp.weixin.qq.com/cgi-bin/loginqrcode?action=ask&token=&lang=zh_CN&f=json&ajax=1&random=',
        'login_auth'  => 'https://mp.weixin.qq.com/cgi-bin/loginauth?action=ask&token=&lang=zh_CN&f=json&ajax=1',
        'login'       => 'https://mp.weixin.qq.com/cgi-bin/bizlogin?action=login&lang=zh_CN',
    ];

    private $_redirect_url;

    private function _getCookieFile()
    {
        return  dirname(__FILE__).'/cookie/'.$this->index.'.cookie';
    }

    private function _getImgPath()
    {
        return dirname(__FILE__).'/QrImgs/'.$this->_qrName();
    }

    private function _qrName()
    {
        return 'qrcode_'.$this->index.'.png';
    }

    private function _log($msg)
    {
        $msg = is_array($msg) ? json_encode($msg) : $msg;
        $filename = date('Y-m-d').'.log';
        $path = dirname(__FILE__).'/WxLogin/';
        $this->_setPath($path, $filename);
        file_put_contents($path.$filename, '['.date('Y-m-d H:i:s').'] local.INFO: '.$msg."\r\n", FILE_APPEND);
    }

    private function _setPath($path = '', $filename = '')
    {
        if (!is_dir($path)) {
            mkdir(dirname($path.$filename), 0777);
        }
    }

    private function _setToken($token = '')
    {
        // 自行设置
    }

    private function _getToken()
    {
        // 日志里
    }

    public function _getRandom()
    {
        return '0.'.mt_rand((int) 1000000000000000, (int) 9999999999999999);
    }

    private function _getError($code = 0)
    {
        switch ($code) {
            case '-1':
                return '系统错误，请稍候再试。';
                break;
            case '-2':
                return '帐号或密码错误。';
                break;
            case '-23':
                return '您输入的帐号或者密码不正确，请重新输入。';
                break;
            case '-21':
                return '不存在该帐户。';
                break;
            case '-7':
                return '您目前处于访问受限状态。';
                break;
            case '-26':
                return '该公众会议号已经过期，无法再登录使用。';
                break;
            case '0':
                return '成功登录，正在跳转...';
                break;
            case '-25':
                return "海外帐号请在公众平台海外版登录,<a href='http://admin.wechat.com/'>点击登录</a>";
                break;

            default:
                return '未知错误。';
                break;
        }
    }

    public function init($data = [])
    {
        // 入参判断
        if (!isset($data['index']) || !isset($data['username']) || !isset($data['pwd'])) {
            return ['status' => false, 'msg' => '参数错误!'];
        }
        $this->index    = $data['index'];
        $this->username = $data['username'];
        $this->pwd      = $data['pwd'];
        if ($this->_getToken()) {
            return true;
        } else {
            return $this->do_login();
        }
    }

    /**
     * 登陆授权
     */
    protected function do_login()
    {
        // 登陆
        $login_data = $this->login();
        if ($login_data['base_resp']['ret'] == 0) {
            // 登陆成功 请求二维码
            $this->_saveQrCode();

            // 心跳验证
            $_link  = $this->_apis['login_ask'];
            $_index = 1;

            // 参数
            $data = [
                'cookie_file' => 1,
                'refer' => $this->_redirect_url,
            ];

            while (true) {
                if ($_index > 30) {
                    break;
                }

                $result = json_decode($this->_send($_link.$this->_getRandom(), $data), true);
                $this->_log(json_encode($result));

                // 二维码验证状态
                // status: 0-未打开 4-打开未扫码 2-打开未授权 1-打开已授权 3-过期
                $status = isset($result['status']) ? $result['status'] : 0;
                if (1 == $status) {
                    if (2 == $result['user_category']) {
                        $this->_log('登陆成功！');
                        break;
                        $_link = $this->_apis['login_auth'];
                    } elseif (1 == $result['user_category']) {
                        $_link = $_apis['auth'];
                    } else {
                        $this->_log('Login error!');
                    }
                } elseif (4 == $status) {
                    $this->_log('打开未扫码或用户未授权');
                } elseif (3 == $status) {
                    $this->_log('登录超时');
                    break;
                } else {
                    if ($_link == $this->_apis['login_ask']) {
                        $this->_log('请打开'.$this->_qrName().'，用微信扫码');
                    } else {
                        $this->_log('等待确认');
                    }
                }
                sleep(2);
                ++$_index;
            }

            if ($_index >= 60) {
                $this->_log('time out!');

                return ['status' => 0, 'msg' => 'time out!'];
            }

            $this->_log('start authorized!');

            // 获取token
            $data['post'] = ['lang' => 'zh_CN', 'f' => 'json', 'ajax' => 1, 'random' => $this->_getRandom(), 'token' => ''];
            $auth_result = $this->_send($this->_apis['login'], $data);
            $this->_log($auth_result);

            $auth_result = json_decode($auth_result, true);
            if ($auth_result['base_resp']['ret'] != 0) {
                return;
            }

            //跳转路径
            $redirect_url = $auth_result['redirect_url'];

            //获取cookie
            if (preg_match('/token=([\d]+)/i', $redirect_url, $match)) {
                $this->_log('验证成功,token: '.$match[1]);

                // return $this->test($match[1]);
            }

            return ['status' => 1, 'msg' => 'success!'];
        } else {
            return ['status' => $login_data['base_resp']['ret'], 'msg' => $this->_getError($login_data['base_resp']['ret'])];
        }

        return $login_data;
    }

    /**
     * 测试请求
     */
    public function get($link = '', $token = '', $index)
    {
        $this->index = $index;
        $link   = $link."&token=".$token;
        $result = $this->_send($link, ['cookie_file' => 1]);
        $this->_log($result);

        return $result;
    }

    /**
     * 登陆验证
     */
    protected function login()
    {
        $data = [
            'cookie_file' => 1,
            'post' => [
                'username' => $this->username,
                'pwd'      => $this->pwd,
                'f'        => 'json',
            ],
        ];
        $login_data = json_decode($this->_send($this->_apis['start_login'], $data), true);

        if ($login_data['base_resp']['ret'] == 0) {
            $this->_redirect_url = $this->_apis['home'].$login_data['redirect_url'];
        }

        return $login_data;
    }

    /**
     * 保存二维码
     */
    protected function _saveQrCode()
    {
        $result = $this->_send($this->_apis['qrcode'], ['cookie_file' => 1]);
        $fp     = fopen($this->_getImgPath(), 'wb+') or die('open fails');
        fwrite($fp, $result) or die('fwrite fails');
        fclose($fp);
    }

    /**
     * Curl请求
     */
    private function _send($url, $data = [])
    {
        $ch = curl_init();

        $headers = [
            'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36',
            'Referer:https://mp.weixin.qq.com/',
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (isset($data['post'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data['post']));
        }

        if (isset($data['cookie_file'])) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_getCookieFile());
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_getCookieFile());
        }

        if (isset($data['refer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $data['refer']);
        }

        $return = curl_exec($ch);
        curl_close($ch);

        return $return;
    }
}
