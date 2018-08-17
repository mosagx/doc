<?php

class WxLogin
{
    private $username;

    private $pwd;

    private $index;

    private $_apis = [
        'home'   => 'https://mp.weixin.qq.com',
        'login'  => 'https://mp.weixin.qq.com/cgi-bin/bizlogin?action=startlogin',
        'qrcode' => 'https://mp.weixin.qq.com/cgi-bin/loginqrcode?action=getqrcode&param=4300&rd=120',
        'auth'   => 'https://mp.weixin.qq.com/cgi-bin/loginauth?action=ask&token=&lang=zh_CN&f=json&ajax=1'
    ];

    private function _getCookieFile()
    {
        return  dirname(__FILE__).'/cookie/cookie_'.$this->index.'.txt';
    }

    private function _getImgPath()
    {
        return dirname(__FILE__).'/QrImgs/'.$this->_qrName();
    }

    private function _qrName()
    {
        return "qrcode_{$this->index}.png";
    }

    private function _log($msg)
    {
        $msg = is_array($msg) ? json_encode($msg) : $msg;
        $filename = date('Y-m-d').'.log';
        $path = dirname(__FILE__).'/logs/WxLogin/';
        if (!is_dir($path)) @mkdir(dirname($path.$filename), 0777);
        file_put_contents($path.$filename, '['.date('Y-m-d H:i:s')."] local.INFO: ".$msg."\r\n", FILE_APPEND );
    }

    private function _setToken()
    {

    }

    private function _getToken()
    {

    }

    private function _getRandom()
    {
        return '0.'.mt_rand(1000000000000000, 9999999999999999);
    }

    

    private function _getError($code = 0)
    {
        switch ($code) {
            case '-1':
                return "系统错误，请稍候再试。";
                break;
            case '-2':
                return "帐号或密码错误。";
                break;
            case '-23':
                return "您输入的帐号或者密码不正确，请重新输入。";
                break;
            case '-21':
                return "不存在该帐户。";
                break;
            case '-7':
                return "您目前处于访问受限状态。";
                break;
            case '-26':
                return "该公众会议号已经过期，无法再登录使用。";
                break;
            case '0':
                return "成功登录，正在跳转...";
                break;
            case '-25':
                return "海外帐号请在公众平台海外版登录,<a href='http://admin.wechat.com/'>点击登录</a>";
                break;
            
            default:
                return "未知错误。";
                break;
        }
    }

    protected function _saveQrCode()
    {

    }

    private function _send($url, $data = [])
    {
        $ch = curl_init();

        $agent = array_key_exists('agent', $data) ? $data['agent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        if (array_key_exists('post', $data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);  
        }
    }
    
}