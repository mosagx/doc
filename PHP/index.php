<?php

require_once 'WX/WxLogin.php';
require_once 'WX/Match.php';

set_time_limit(0);
$wx_login = new WxLogin();
$match    = new Match();

$data = [
    'index'    => 735364431,
    'username' => '735364431@qq.com',     //wx公众帐号
    'pwd'      => md5(''),   //wx公众帐号密码
];
$data1 = [
    'index'    => 1186105500,
    'username' => '1186105500@qq.com',     //wx公众帐号
    'pwd'      => md5(''),   //wx公众帐号密码
];

$url = [
    'home'       => 'https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN',
    'setting'    => 'https://mp.weixin.qq.com/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN',
    'user'       => 'https://mp.weixin.qq.com/misc/useranalysis?action=attr&begin_date=2018-08-21&end_date=2018-08-21&lang=zh_CN',
    'getheadimg' => 'https://mp.weixin.qq.com/misc/getheadimg?token=347685241&fakeid=3088538967&r=593830',
    'getqrcode'  => 'https://mp.weixin.qq.com/misc/getqrcode?fakeid=3088538967'
];

// $return = $wx_login->init($data);

// $fakeid = 3088538967;

// var_dump($head);die;
// echo $ns;

// $return = $match->getBizInfo();
// $return = $match->getFans();
$return = $match->getArticle();

// $return = object_array($return);




// var_dump($return);
// die;

$return = json_encode($return);
header("content-type:application/json");
echo $return;

