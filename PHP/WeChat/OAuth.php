<?php

namespace App\Libs\WeChat;

use App\Libs\WeChat\Base as WechatBase;

class OAuth extends WechatBase
{
    public $api_wx_url;

    public $yfb_open_url;

    public function __construct()
    {
        parent::__construct();

        $this->api_wx_url = config('wechat.API_URL');

        $this->yfb_open_url = config('wechat.YFB_OPEN_URL');

    }

    public function getUserInfo($openid)
    {
        $url  = $this->api_wx_url.'/cgi-bin/user/info?access_token='.$this->getWxToken().'&openid='.$openid.'&lang=zh_CN';
        $info = file_get_contents($url);
        $info = json_decode($info,true);
        $info['nickname'] = $this->userTextEncode($info['nickname']); ////替换特殊符号及表情
        $info = json_encode($info);
        return $info;
    }

    /**
     * 获取微信Token
     */
    public function getWxToken()
    {
        $access_token = $this->cache->get('WX_TOKEN');
        if (!$access_token) {
            $url    = $this->yfb_open_url.'/cgi-bin/token/d4d3f2f2fd220900920e6b67a76f4324/?appid='.$this->appid.'&access_token='.$this->getOpenToken();
            $rq     = file_get_contents($url);
            $result = json_decode($rq, true);
            if(!empty($result['access_token'])){
                $access_token = $result['access_token'];
                $this->cache->put('YFB_OPEN_TOKEN', $access_token, $result['expires_in']);
            }else{
                die(__FUNCTION__.':ERROR');
            }
        }
        return $access_token;
    }

    /**
     * 获取开放 Token
     */
    public function getOpenToken()
    {
        $token = $this->cache->get('YFB_OPEN_TOKEN');
        if (!$token) {
            $url    = $this->yfb_open_url.'/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
            $rq     = file_get_contents($url);
            $result = json_decode($rq, true);
            if(!empty($result['access_token'])){
                $token = $result['access_token'];
                $this->cache->put('YFB_OPEN_TOKEN', $token, 120);
            }else{
                die(__FUNCTION__.':ERROR');
            }
        }
        return $token;
    }
}
