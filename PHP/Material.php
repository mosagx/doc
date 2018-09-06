<?php

class Material
{
    private $_apis = [
        'get_material' => 'https://api.weixin.qq.com/cgi-bin/material/get_material',
        'add_news'     => 'https://api.weixin.qq.com/cgi-bin/material/add_news',
        'del_material' => 'https://api.weixin.qq.com/cgi-bin/material/del_material',
        'list'         => 'https://api.weixin.qq.com/cgi-bin/material/batchget_material',
        'upload'       => 'https://api.weixin.qq.com/cgi-bin/material/add_material'
    ];

    // 默认素材
    private $_defaultMat = [
        'articles' => [
            'title'              => 'default',
            "thumb_media_id"     => '1',
            "author"             => 'system',
            "digest"             => 'digest',
            "show_cover_pic"     => 'none',
            "content"            => 'default content',
            "content_source_url" => '1'
        ]
    ];

    // 默认图片
    private $_defaultPic;

    private $media_id;

    private $_token;

    public function init($token)
    {
        // return $this->_apis['del_material'];
        $this->_token = $token;
        
        // list
        $material = $this->_list();
        // if (isset($material['errmsg'])) {
        //     return $material;
        // }

        // if ($material['item']) {
        //     return $this->getBiz($material['item'][0]['content']['news_item'][0]['url']);
        // }
        // unset($material);
        $info = $this->_uploadImg();
        if (isset($info['errmsg'])) {
            return $info;
        }

        // add_news
        $add_result = $this->_add($info['media_id']);
        return $add_result;
        if (isset($add_result['errmsg'])) {
            return $add_result;
        }

        $this->media_id = $add_result['media_id'];

        // get_material
        $get_result = $this->_get();
        if (isset($get_result['errmsg'])) {
            return $get_result;
        }

        // del_material
        // $this->_del();

        return $this->getBiz($get_result['news_item'][0]['url']);
    }

    /**
     * 素材获取
     */
    private function _get()
    {
        return $this->_send('get_material', ['media_id' => $this->media_id]);
    }

    /**
     * 素材列表
     */
    private function _list()
    {
        return $this->_send('list', [
            'type'   => 'news',
            'offset' => 0,
            'count'  => 1
        ]);
    }

    /**
     * 素材添加
     */
    private function _add($media_id)
    {
        return $this->_send('add_news', [
                'title'              => 'default',
                "thumb_media_id"     => $media_id,
                "author"             => 'system',
                "digest"             => 'digest',
                "show_cover_pic"     => 'none',
                "content"            => 'default content',
                "content_source_url" => '1'
        ]);
    }

    /**
     * 删除素材
     */
    private function _del()
    {
        return $this->_send('del_material', ['media_id' => $this->media_id]);
    }

    /**
     * 截取biz
     */
    private function getBiz($link = '') 
    {
        $result = [];
        $mr = preg_match_all('/(\?|&)(.+?)=([^&?]*)/i', $link, $matchs);
        if ($mr !== false) {
            for ($i = 0; $i < $mr; $i++) {
                $result[$matchs[2][$i]] = $matchs[3][$i];
            }
        }
        return isset($result['__biz']) ? $result['__biz'] : '';
    }

    private function _defaultImg()
    {
        return dirname(__FILE__).'\default.jpg';
    }
    
    private function _uploadImg()
    {
        return $this->_send('upload', [
            'media' => new \CURLFile($this->_defaultImg())
        ]);
    }

    /**
     * 接口请求
     */
    private function _send($apis, $data = [])
    {
        $ch = curl_init();
        $timeout = 10;
        if ($apis == 'upload') {
            $host = $this->_apis[$apis].'?access_token='.$this->_token.'&type=image';
            curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $host = $this->_apis[$apis].'?access_token='.$this->_token;
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_URL, $host);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        return json_decode(curl_exec($ch), true);
    }

}