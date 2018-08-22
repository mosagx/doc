<?php
require_once 'WxLogin.php';

class Match
{
    private $_hosts = [
        'home'       => 'https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN',
        'setting'    => 'https://mp.weixin.qq.com/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN',
        'user'       => 'https://mp.weixin.qq.com/misc/useranalysis?action=attr&begin_date=2018-08-21&end_date=2018-08-21&lang=zh_CN',
        'getheadimg' => 'https://mp.weixin.qq.com/misc/getheadimg?token=347685241&fakeid=3088538967&r=593830',
        'getqrcode'  => 'http://open.weixin.qq.com/qr/code?username='
    ];

    public function getBizInfo()
    {
        $WL      = new WxLogin();
        $return  = [];
        $content = $WL->get($this->_hosts['setting'], 761039539, 735364431);
        $ns      = str_replace([' ', '  ', "\n", "\r", "\t", "&nbsp"], '', $content);

        // 头像 公众号名称 是否认证 微信号 主体名称 二维码 简介 是否为服务号

        // 头像
        preg_match_all('/class="weui-desktop-account__thumb".*?64/is', $ns, $head_img);
        if ($head_img[0]) {
            $return['head_img'] = trim(str_replace(['class="weui-desktop-account__thumb"src="', '64'], ['', 0], $head_img[0][0]));
        }

        // 公众号名称
        preg_match_all('/class="weui-desktop-setting__item__info">.*?</is', $ns, $mp_info);
        // return $mp_info;
        if ($mp_info[0]) {
            if (count($mp_info[0]) == 10) {
                $return['mp_name']    = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][0]));
                $return['weixinname'] = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][1]));
                $return['type']       = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][2]));
                $return['desc']       = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][3]));
                $return['user_name']  = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][9]));
                $return['belong']     = trim(str_replace(['class="weui-desktop-setting__item__info">', '<', ';'], '', $mp_info[0][6]));
            } else {
                $return['mp_name']    = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][0]));
                $return['weixinname'] = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][1]));
                $return['type']       = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][2]));
                $return['desc']       = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][3]));
                $return['user_name']  = trim(str_replace(['class="weui-desktop-setting__item__info">', '<'], '', $mp_info[0][11]));
                $return['belong']     = trim(str_replace(['class="weui-desktop-setting__item__info">', '<', ';'], '', $mp_info[0][7]));
            }
            
        }

        // 是否认证
        preg_match_all('/class="weui-desktop-account__typeweui-desktop-account__type_splitweui-desktop__small-screen-hide">.*?</is', $ns, $auth);
        if ($auth[0]) {
            $return['auth'] = trim(str_replace(['class="weui-desktop-account__typeweui-desktop-account__type_splitweui-desktop__small-screen-hide">', '<'], '', $auth[0][0]));
        }

        // 二维码
        $return['qrcode'] = $this->_hosts['getqrcode'].$return['user_name'];

        unset($ns);
        return $return;
    }



}