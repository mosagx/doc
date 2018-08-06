<?php

class ShanPiaoYun
{
    public static $spy_url         = "https://novel.shanpiaoyun.cn/openapi/";
    public static $spy_version     = "v1";
    public static $spy_account_id  = 245675;
    public static $spy_account_key = "40b7nlnfzswlxdkbhjivnh03gpyx27yg";



    // ------------------------------------------------------------ 账号

    /**
     * 获取账号信息
     */
    public static function getAccount($data = []) 
    {
        return self::Do($data, 'getAccount');
    }

    /**
     * 更新账号信息
     */
    public static function setAccount($data = []) 
    {
        return self::Do($data, 'setAccount');
    }

    // ------------------------------------------------------------ 微信公众号

    /**
     * 获取公众号列列表
     */
    public static function getWe($data = []) 
    {
        return self::Do($data, 'getWe');
    }

    /**
     * 获取公众号详情
     */
    public static function getWeDetail($data = []) 
    {
        return self::Do($data, 'getWeDetail');
    }

    /**
     * 设置公众号Secret
     */
    public static function setWeSecret($data = []) 
    {
        return self::Do($data, 'setWeSecret');
    }

    /**
     * 上传公众号校验文件
     */
    public static function setWeVerifyFile($data = []) 
    {
        return self::Do($data, 'setWeVerifyFile');
    }

    /**
     * 上传公众号客服图片
     */
    public static function setWeKefu($data = []) 
    {
        return self::Do($data, 'setWeKefu');
    }

    /**
     * 获取公众号订单
     */
    public static function getWeOrder($data = []) 
    {
        return self::Do($data, 'getWeOrder');
    }

    /**
     * 获取公众号结算单列表
     */
    public static function getWeSettlement($data = []) 
    {
        return self::Do($data, 'getWeSettlement');
    }

    /**
     * 设置公众号关注类型
     */
    public static function setWeGuanzhu($data = []) 
    {
        return self::Do($data, 'setWeGuanzhu');
    }

    // ------------------------------------------------------------ 公众号用户

    /**
     * 获取用户列表
     */
    public static function getUser($data = []) 
    {
        return self::Do($data, 'getUser');
    }

    /**
     * 获取用户详细信息
     */
    public static function getUserDetail($data = []) 
    {
        return self::Do($data, 'getUserDetail');
    }

    /**
     * 获取用户订单列表
     */
    public static function getUserOrder($data = []) 
    {
        return self::Do($data, 'getUserOrder');
    }
    
    /**
     * 增加用户书币
     */
    public static function addUserScore($data = []) 
    {
        return self::Do($data, 'addUserScore');
    }

    // ------------------------------------------------------------ 推广链接

    /**
     * 创建推广链接
     */
    public static function createUrl($data = []) 
    {
        return self::Do($data, 'createUrl');
    }    

    /**
     * 删除推广链接
     */
    public static function deleteUrl($data = []) 
    {
        return self::Do($data, 'deleteUrl');
    }

    /**
     * 获取推广链接列表
     */
    public static function getUrl($data = []) 
    {
        return self::Do($data, 'getUrl');
    }
    
    /**
     * 获取推广链接
     */
    public static function getUrlDetail($data = []) 
    {
        return self::Do($data, 'getUrlDetail');
    }

    /**
     * 获取推广链接订单列表
     */
    public static function getUrlOrder($data = []) 
    {
        return self::Do($data, 'getUrlOrder');
    }
    
    /**
     * 获取推广链接用户列表
     */
    public static function getUrlUser($data = []) 
    {
        return self::Do($data, 'getUrlUser');
    }

    // ------------------------------------------------------------ 平台小说 

    /**
     * 获取小说列表
     */
    public static function getBook($data = []) 
    {
        return self::Do($data, 'getBook');
    } 

    /**
     * 获取小说详情
     */
    public static function getBookDetail($data = []) 
    {
        return self::Do($data, 'getBookDetail');
    }

    /**
     * 获取小说前20章详情
     */
    public static function getBookChapter($data = []) 
    {
        return self::Do($data, 'getBookChapter');
    }

    /**
     * 获取小说订单列表
     */
    public static function getBookOrder($data = []) 
    {
        return self::Do($data, 'getBookOrder');
    }

    /**
     * 获取小说关注列表
     */
    public static function getBookGuanzhu($data = []) 
    {
        return self::Do($data, 'getBookGuanzhu');
    }

    /**
     * 获取小说目录
     */
    public static function getBookCatalog($data = []) 
    {
        return self::Do($data, 'getBookCatalog');
    }

    // ------------------------------------------------------------ 平台充值活动

    /**
     * 获取平台充值活动列表
     */
    public static function getActivity($data = []) 
    {
        return self::Do($data, 'getActivity');
    }

    /**
     * 获取充值活动详情
     */
    public static function getActivityDetail($data = []) 
    {
        return self::Do($data, 'getActivityDetail');
    }

    /**
     * 获取充值活动订单列表
     */
    public static function getActivityOrder($data = []) 
    {
        return self::Do($data, 'getActivityOrder');
    }

    // ------------------------------------------------------------ Base

    /**
     * 验签
     */
    private static function getSign($data = [])
    {
        ksort($data);
        $string = '';
        foreach ($data as $key => $value) {
            $string .= $key.$value;
        }
        return md5($string.self::$spy_account_key);
    }

    /**
     * 参数转换
     */
    private static function paramToUrl($data = [])
    {
        $str = '';
        foreach ($data as $key => $value) {
            $str .= "&{$key}=".urlencode($value);
        }
        return $str.'&sig='.self::getSign($data);
    }

    /**
     * 执行
     */
    public static function Do($data = [], $method = '')
    {
        $data['account_id'] = self::$spy_account_id;
        $data['timestamp']  = time();
        $host  = self::$spy_url.self::$spy_version.'/'.$method.'?';
        $host .= self::paramToUrl($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        if ($response) {
            return json_decode($response,true);
        } else {
            error_log('Curl error: ' . curl_error($ch).'无返回参数');
        }
        curl_close($ch);
    }
}

