<?php
require_once 'WxLogin.php';

class Match
{
    private $_hosts = [
        'home'              => 'https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN',
        'setting'           => 'https://mp.weixin.qq.com/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN',
        'user'              => 'https://mp.weixin.qq.com/misc/useranalysis?action=attr&begin_date=2018-08-21&end_date=2018-08-21&lang=zh_CN',
        'fans'              => 'https://mp.weixin.qq.com/misc/useranalysis?=&lang=zh_CN',
        'fans_data'         => 'https://mp.weixin.qq.com/misc/useranalysis?action=attr&lang=zh_CN',
        'getheadimg'        => 'https://mp.weixin.qq.com/misc/getheadimg?token=347685241&fakeid=3088538967&r=593830',
        'getqrcode'         => 'http://open.weixin.qq.com/qr/code?username=',
        'yuanchuang'        => 'https://mp.weixin.qq.com/cgi-bin/plugindetails?t=service/profile&pluginid=10042&action=intro&lang=zh_CN',
        'article_list'      => 'https://mp.weixin.qq.com/misc/appmsganalysis?action=all&order_direction=2&lang=zh_CN&f=json',
        // 'article_data'      => 'https://mp.weixin.qq.com/misc/appmsganalysis?action=report&lang=zh_CN',
        'article_list_info' => 'https://mp.weixin.qq.com/cgi-bin/newmasssendpage?lang=zh_CN&f=json&ajax=1',
        'illegal'           => 'https://mp.weixin.qq.com/cgi-bin/illegalrecord?t=violation/list&lang=zh_CN&f=json'
    ];

    private function getContent(...$host)
    {
        $WL     = new WxLogin();
        $return = [];
        $date   = date('Y-m-d', strtotime('-1 day'));
        
        switch ($host[0]) {
            case 'fans_data':
                $link = $this->_hosts['fans_data'].'&begin_date='.$date.'&end_date='.$date;
                break;
            case 'article_list':
                $link = $this->_hosts['article_list'].'&end_date='.$date.'&begin_date='.date('Y-m-d', strtotime('-30 day'));
                break;
            case 'article_list_info':
                if (isset($host[1])) {
                    $link = $this->_hosts['article_list_info'].'&begin='.$host[1];
                } else {
                    $link = $this->_hosts[$host[0]];
                }
                break;
            case 'illegal':
                if (isset($host[1])) {
                    $link = $this->_hosts['article_list_info'].'&page='.$host[1];
                } else {
                    $link = $this->_hosts[$host[0]];
                }
                break;
            
            default:
                $link = $this->_hosts[$host[0]];
                break;
        }
        // $content = $WL->get($link, 1598858939, 'duorou91');  // 多肉植物
        $content = $WL->get($link, 1509288105, 735364431);      // best生活通
        // $content = $WL->get($link, 1343894970, 1186105500);   // 优粉吧
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
        $article_info = json_decode($this->getContent('article_list_info'), true);
        if ($article_info['base_resp']['ret'] != 0) {
            return ['status' => 0, 'msg' => $article_info['base_resp']['err_msg']];
        }
        $total = $article_info['total_count'];
        unset($article_info);
        $star_time = strtotime(date('Y-m-d', strtotime('-30 day')));
        $pages = ceil($total/7);
        for ($i=0; $i < $pages; $i++) { 
            $begin = abs($i * 7 - 1);
            $article_data =  json_decode($this->getContent('article_list_info', $begin), true);
            if ($article_data['base_resp']['ret'] != 0) {
                return ['status' => 0, 'msg' => $article_data['base_resp']['err_msg']];
            }
            for ($j=0; $j < count($article_data['sent_list']); $j++) { 
                if ($article_data['sent_list'][$j]['sent_info']['time'] < $star_time) {
                    break 2;    // 时间不匹配 跳出外层
                }
                if (!$article_data['sent_list'][$j]['appmsg_info']) {
                    break;      // 无发文跳出本次
                }
                for ($k=0; $k < count($article_data['sent_list'][$j]['appmsg_info']); $k++) {                   
                    $row = [];

                    $row['title']        = $article_data['sent_list'][$j]['appmsg_info'][$k]['title'];
                    $row['face_img']     = str_replace('?wx_fmt=jpeg', '', $article_data['sent_list'][$j]['appmsg_info'][$k]['cover']);  // 去除防拷贝
                    $row['link']         = $article_data['sent_list'][$j]['appmsg_info'][$k]['content_url'];
                    $row['is_deleted']   = $article_data['sent_list'][$j]['appmsg_info'][$k]['is_deleted'];
                    $row['like_num']     = $article_data['sent_list'][$j]['appmsg_info'][$k]['like_num'];
                    $row['read_num']     = $article_data['sent_list'][$j]['appmsg_info'][$k]['read_num'];
                    $row['mid']          = $article_data['sent_list'][$j]['appmsg_info'][$k]['appmsgid'];
                    $row['publish_time'] = $article_data['sent_list'][$j]['sent_info']['time'];
                    $row['type']         = $article_data['sent_list'][$j]['type']; 
                    $row['idx']          = $k + 1;

                    $return['list'][] = $row;
                }
            }
            unset($article_data);
        }

        $first_article = json_decode($this->getContent('article_list_info', $total - 1), true);
        if ($first_article['base_resp']['ret'] != 0) {
            return ['status' => 0, 'msg' => $first_article['base_resp']['err_msg']];
        }
        $return['first_send'] = date('Y-m-d H:i:s', $first_article['sent_list'][0]['sent_info']['time']);
        unset($first_article);
        return $return;
    }

    /**
     * 违规信息
     */
    public function illegalRecord()
    {
        $return       = [];
        $illegal_info = json_decode($this->getContent('illegal'), true);
        $return['illegal'] = [];
        if (!$illegal_info) {
            return $return;
        }
        $total = $illegal_info['illegal_record_count'];
        $pages = ceil($total/10);
        
        for ($i=0; $i < $pages; $i++) { 
            $illegal_data = json_decode($this->getContent('illegal', $i +1), true);
            for ($i=0; $i < count($illegal_data['illegal_record_list']); $i++) { 
                $row = [];

                $row['title']             = $illegal_data['illegal_record_list'][$i]['title'];
                $row['link']              = $illegal_data['illegal_record_list'][$i]['content'];
                $row['type']              = $illegal_data['illegal_record_list'][$i]['illegal_type'];
                $row['desc']              = $illegal_data['illegal_record_list'][$i]['illegal_type_desc'];
                $row['content_type']      = $illegal_data['illegal_record_list'][$i]['content_type'];
                $row['content_type_desc'] = $illegal_data['illegal_record_list'][$i]['content_type_desc'];
                $row['created_at']        = $illegal_data['illegal_record_list'][$i]['create_timestamp'];
            }
        }

        return $return;
    }

    /**
     * 详细数据
     */
    public function getDetail()
    {
        $return       = [];
        $article_list = json_decode($this->getContent('article_list'), true);
        if ($article_list['base_resp']['ret'] != 0) {
            return ['status' => 0, 'msg' => $article_list['base_resp']['err_msg']];
        }
        $article_data = json_decode($article_list['total_article_data'], true);
        for ($i=0; $i < count($article_data['list']); $i++) { 
            $mid_idx = explode('_', $article_data['list'][$i]['msgid']);

            $return['list'][$i]['title']        = $article_data['list'][$i]['title'];
            $return['list'][$i]['mid']          = $mid_idx[0];
            $return['list'][$i]['idx']          = $mid_idx[1];
            $return['list'][$i]['publish_date'] = $article_data['list'][$i]['publish_date'];
            $return['list'][$i]['read_num']     = $article_data['list'][$i]['int_page_read_user'];
            $return['list'][$i]['ori_read_num'] = $article_data['list'][$i]['ori_page_read_user'];
            $return['list'][$i]['user_source']  = $article_data['list'][$i]['user_source'];
        }
        return $return;
    }

}