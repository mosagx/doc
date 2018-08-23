<?php
require_once 'WxLogin.php';

class Match
{
    private $_hosts = [
        'home'         => 'https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN',
        'setting'      => 'https://mp.weixin.qq.com/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN',
        'user'         => 'https://mp.weixin.qq.com/misc/useranalysis?action=attr&begin_date=2018-08-21&end_date=2018-08-21&lang=zh_CN',
        'fans'         => 'https://mp.weixin.qq.com/misc/useranalysis?=&lang=zh_CN',
        'fans_data'    => 'https://mp.weixin.qq.com/misc/useranalysis?action=attr&lang=zh_CN',
        'getheadimg'   => 'https://mp.weixin.qq.com/misc/getheadimg?token=347685241&fakeid=3088538967&r=593830',
        'getqrcode'    => 'http://open.weixin.qq.com/qr/code?username=',
        'yuanchuang'   => 'https://mp.weixin.qq.com/cgi-bin/plugindetails?t=service/profile&pluginid=10042&action=intro&lang=zh_CN',
        'article_list' => 'https://mp.weixin.qq.com/misc/appmsganalysis?action=all&order_direction=2&lang=zh_CN&f=json',
        'article_data' => 'https://mp.weixin.qq.com/misc/appmsganalysis?action=report&lang=zh_CN'
    ];

    private function getContent($host)
    {
        $WL     = new WxLogin();
        $return = [];
        $date   = date('Y-m-d', strtotime('-1 day'));
        
        switch ($host) {
            case 'fans_data':
                $link = $this->_hosts['fans_data'].'&begin_date='.$date.'&end_date='.$date;
                break;
            case 'article_list':
                $link = $this->_hosts['article_list'].'&end_date='.$date.'&begin_date='.date('Y-m-d', strtotime('-30 day'));
                break;
            
            default:
                $link = $this->_hosts[$host];
                break;
        }
        $content = $WL->get($link, 1678609170, 735364431);
        // $content = $WL->get($link, 733275141, 1186105500);
        $ns      = str_replace([' ', '  ', "\n", "\r", "\t"], '', $content);
        preg_match_all('/登录超时.*?/is', $ns, $validate);
        if ($validate[0]) {
            return false;
        } else {
            return $ns;
        }
    }

    /**
     * 基础数据
     */
    public function getBizInfo()
    {
        $return = [];
        $ns     = $this->getContent('setting');
        if (!$ns) {
            return ['status' => 0, 'msg' => '登陆超时'];
        }

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
                $return['auth']       = '微信认证';
            }
            
        }

        // 是否认证
        preg_match_all('/class="weui-desktop-account__typeweui-desktop-account__type_splitweui-desktop__small-screen-hide">.*?</is', $ns, $auth);
        if ($auth[0]) {
            $return['auth'] = trim(str_replace(['class="weui-desktop-account__typeweui-desktop-account__type_splitweui-desktop__small-screen-hide">', '<'], '', $auth[0][0]));
        }

        // 二维码
        if (isset($return['user_name'])) {
            $return['qrcode'] = $this->_hosts['getqrcode'].$return['user_name'];
        }

        unset($ns);
        return $return;
    }

    /**
     * 粉丝数据
     */
    public function getFans()
    {
        $return    = [];
        $fans      = $this->getContent('fans');
        $fans_data = $this->getContent('fans_data');
        if (!$fans || !$fans_data) {
            return ['status' => 0, 'msg' => '登陆超时'];
        }

        // 总粉丝
        preg_match_all('/date:"'.date('Y-m-d', strtotime('-1 day')).'".*?}/is', $fans, $today);
        if ($today[0]) {
            preg_match_all('/cumulate_user:.*?,/is', $today[0][0], $total);
            $return['fans']['total_fans'] = trim(str_replace(['cumulate_user:', ','], '', $total[0][0]));
        }

        // 30日累计粉丝
        preg_match_all('/netgain_user:.*?,/is', $fans, $netgain_user);
        if ($netgain_user[0]) {
            $netgain_fans = 0;
            for ($i=0; $i < count($netgain_user[0]); $i++) { 
                $netgain_fans += trim(str_replace(['netgain_user:', ','], '', $netgain_user[0][$i]));
            }
            unset($netgain_user);
            $return['fans']['netgain_fans'] = $netgain_fans;
        }

        unset($fans);

        // 性别比例 
        preg_match_all('/genders:.*?]/is', $fans_data, $genders);
        if ($genders[0]) {
            $genders_list = str_replace(['genders:[', ']'], '', $genders[0][0]);
            preg_match_all('/name:.*?,/is', $genders_list, $name);
            preg_match_all('/count:.*?}/is', $genders_list, $count);
            unset($genders_list);
            for ($i=0; $i < count($name[0]); $i++) { 
                $return['fans']['genders'][$i]['name']  = trim(str_replace(['name:', ','], '', $name[0][$i]));
                $return['fans']['genders'][$i]['count'] = trim(str_replace(['count:+(', ')||0}'], '', $count[0][$i]));
            }
        }
        
        // 地域比例 
        preg_match_all('/regions:.*?]/is', $fans_data, $regions);
        if ($regions[0]) {
            $regions_list = str_replace(['regions:[', ']'], '', $regions[0][0]);
            preg_match_all('/region_name:.*?}/is', $regions_list, $region_name);
            preg_match_all('/count:.*?}/is', $regions_list, $count);
            unset($regions_list);
            $num = count($region_name[0]) >= 10 ? 10 : count($region_name[0]); 
            for ($i=0; $i < $num; $i++) { 
                $return['fans']['regions'][$i]['region_name'] = trim(str_replace(['region_name:', '}'], '', $region_name[0][$i]));
                $return['fans']['regions'][$i]['count']       = trim(str_replace(['count:+(', ')||0}'], '', $count[0][$i]));
            }
        }

        // 终端占比 
        preg_match_all('/platforms:.*?]/is', $fans_data, $platforms);
        if ($platforms[0]) {
            $platforms_list = str_replace(['platforms:[', ']'], '', $platforms[0][0]);
            preg_match_all('/name:.*?,/is', $platforms_list, $platforms_name);
            preg_match_all('/count:.*?}/is', $platforms_list, $count);
            unset($platforms_list);
            for ($i=0; $i < count($platforms_name[0]); $i++) { 
                $return['fans']['platforms'][$i]['name']  = trim(str_replace(['name:', '||"未知",'], '', $platforms_name[0][$i]));
                $return['fans']['platforms'][$i]['count'] = trim(str_replace(['count:+(', ')||0}'], '', $count[0][$i]));
            }
        }
        
        // 机型占比
        preg_match_all('/devices:.*?]/is', $fans_data, $devices);
        if ($devices[0]) {
            $devices_list = str_replace(['devices:[', ']'], '', $devices[0][0]);
            preg_match_all('/value:.*?,/is', $devices_list, $devices_name);
            preg_match_all('/count:.*?}/is', $devices_list, $count);
            unset($devices_list);
            for ($i=0; $i < count($devices_name[0]); $i++) { 
                $return['fans']['devices'][$i]['name']  = trim(str_replace(['value:', ','], '', $devices_name[0][$i]));
                $return['fans']['devices'][$i]['count'] = trim(str_replace(['count:+(', ')||0}'], '', $count[0][$i]));
            }
        }

        unset($fans_data);
        return $return;
    }

    /**
     * 文章数据
     */
    public function getArticle()
    {
        $return       = [];
        $article_list = json_decode($this->getContent('article_list'), true);
        if ($article_list['base_resp']['ret'] != 0) {
            return ['status' => 0, 'msg' => $article_list['base_resp']['err_msg']];
        }

        $article_data = json_decode($article_list['total_article_data'], true);
        for ($i=0; $i < count($article_data['list']); $i++) { 
            $return['list'][$i]['title']        = $article_data['list'][$i]['title'];
            $return['list'][$i]['msgid']        = $article_data['list'][$i]['msgid'];
            $return['list'][$i]['publish_date'] = $article_data['list'][$i]['publish_date'];
            $return['list'][$i]['read_num']     = $article_data['list'][$i]['int_page_read_user'];
            $return['list'][$i]['user_source']  = $article_data['list'][$i]['user_source'];
        }

        // $article_summary_data = json_decode($article_list['article_summary_data'], true);

        return $return;
    }

}