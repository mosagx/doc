<?php
// $arr = array(
//     'username'=>$_POST['user'], //wx公众帐号
//     'pwd'=>md5($_POST['pass']), //wx公众帐号密码
//     'f'=>'json'
// );

$arr = array(
    'username'=>'735364431@qq.com', //wx公众帐号
    'pwd'=>md5('cj13857042902'), //wx公众帐号密码
    'f'=>'json'
);

// if (isset($_POST['code'])) {
//     $arr['imgcode'] = $_POST['code'];
// }

$cookie_file = dirname(__FILE__).'\cookie\cookie_735364431@qq.txt';

$headers = array(
    'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36',
    'Referer:https://mp.weixin.qq.com/',
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://mp.weixin.qq.com/cgi-bin/bizlogin?action=startlogin');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0 );
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2 );
curl_setopt($curl, CURLOPT_TIMEOUT, 10 );   
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file); 
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arr));
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

// if (!empty($arr['imgcode'])) {
//     curl_setopt($curl, CURLOPT_COOKIEFILE, $file);
// }

$result = json_decode(curl_exec($curl),true);

if ($result['base_resp']['ret'] == 0) {
    // $qr = 'https://mp.weixin.qq.com/cgi-bin/loginqrcode?action=ask&token=&lang=zh_CN&f=json&ajax=1&random=0.'.mt_rand((int)1000000000000000, (int)9999999999999999);
    $qrcode = 'https://mp.weixin.qq.com/cgi-bin/loginqrcode?action=getqrcode&param=4300&rd=120';
    // $redirect = 'https://mp.weixin.qq.com'.$result['redirect_url'];
    $ch = curl_init($qrcode); 
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
    curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file); 
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $content = curl_exec($ch); 
    $fp = fopen(dirname(__FILE__).'/QrImages.png', 'wb+') or die('open fails');
    fwrite($fp, $content) or die('fwrite fails');
    fclose($fp);
    // imagejpeg($content);
    echo $content;
    // var_dump($content);
    curl_close($ch);
} else {
    var_dump($result);
}

curl_close($curl);