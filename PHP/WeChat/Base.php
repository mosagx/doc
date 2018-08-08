<?php

namespace App\Libs\WeChat;

use Illuminate\Support\Facades\Cache;

class Base
{
    public $cache;

    public $appid;

    public $secret;

    public function __construct()
    {
        $this->cache = Cache::store('redis');

        $this->appid = config('wechat.APPID');

        $this->secret = config('wechat.SECRET');
    }


    public function userTextEncode($str)
    {
        if (!is_string($str)) {
            return $str;
        }
        if (!$str || 'undefined' == $str) {
            return '';
        }

        $text = json_encode($str); //暴露出unicode

        //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, $text); 
        return json_decode($text);
    }

    public function userTextDecode($str)
    {
        $text = json_encode($str); //暴露出unicode
        
        //将两条斜杠变成一条，其他不动
        $text = preg_replace_callback('/\\\\\\\\/i', function ($str) {
            return '\\';
        }, $text); 
        return json_decode($text);
    }
}
