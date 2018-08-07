<?php

/**
 * Really Simple Syndication
 */
class RSS
{
    public $title;
    public $link;
    public $description;
    public $language = 'en-us';
    public $pubDate;
    public $item;
    public $tags;

    public function RSS()
    {
        $this->items = [];
        $this->tags = [];
    }

    public function addItem($item)
    {
        $this->item[] = $item;
    }

    public function setPubDate($when)
    {
        if (!$when) {
            return date('D, d M Y H:i:s ').'GMT';
        } else {
            return $this->pubDate();
        }
    }

    public function addTag($tag, $value)
    {
        $this->tags[$tag] = $value;
    }

    public function out()
    {
        $out = $this->header();
        $out .= "<channel>\n";
        $out .= '<title>'.$this->title."</title>\n";
        $out .= '<link>'.$this->link."</link>\n";
        $out .= '<description>'.$this->description."</description>\n";
        $out .= '<language >'.$this->language."</language >\n";
        $out .= '<pubDate >'.$this->pubDate."</pubDate >\n";

        foreach ($this->tags as $key => $value) {
            $out .= "<$key>$value</$key>\n";
        }
        foreach ($this->items  as $item) {
            $out .= $item->out();
        }

        $out .= "</channel>\n";
        $out .= $this->footer();
        $out = str_replace('&', '&amp', $out);

        return $out;
    }

    public function server($contentType = 'application/xml')
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        header("Content-type: $contentType");
        echo $xml;
    }

    public function header()
    {
        $out = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $out .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";

        return $out;
    }

    public function footer()
    {
        return '</rss>';
    }
}

class RSSItem
{
    public $title;
    public $link;
    public $description;
    public $pubDate;
    public $guid;
    public $tags;
    public $attachment;
    public $length;
    public $mimetype;

    public function RSSItem()
    {
        $this->tags = [];
    }

    public function setPubDate($when)
    {
        if (false == strtotime($when)) {
            $this->pubDate = date('D, d M Y H:i:s', $when).'GMT';
        } else {
            $this->pubDate = date('D, d M Y H:i:s', strtotime($when)).'GMT';
        }
    }

    public function getPubDate()
    {
        if (empty($this->pubDate)) {
            return date('D, d M Y H:i:s').'GMT';
        } else {
            return $this->pubDate;
        }
    }

    public function addTag($tag, $value)
    {
        $this->tags[$tag] = $value;
    }

    public function out()
    {
        $out .= "<item>\n";
        $out .= '<title>'.$this->title."</title>\n";
        $out .= '<link>'.$this->link."</link>\n";
        $out .= '<description>'.$this->description."<description>\n";
        $out .= '<pubDate>'.$this->getPubDate()."</pubDate>\n";

        if ('' != $this->attachment) {
            $out .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";
        }

        if (empty($this->guid)) {
            $this->guid = $this->link;
        }
        $out .= '<guid>'.$this->guid."</guid>\n";

        foreach ($this->tags as $key => $val) {
            $out .= "<$key>$val</$key>\n";
        }
        $out .= "</item>\n";

        return $out;
    }

    public function enclosure($url, $mimetype, $length)
    {
        $this->attachment = $url;
        $this->mimetype   = $mimetype;
        $this->length     = $length;
    }

    /* 实例 -----------------------------------------------
    $feed = new RSS();
    $feed->title       = "RSS Feed 标题";
    $feed->link        = "https://www.baidu.com";
    $feed->description = "RSS 订阅列表描述。";

    $db->query($query);  // 数据库查询
    $result = $db->result;
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        $item = new RSSItem();
        $item->title = $title;
        $item->link  = $link;
        $item->setPubDate($create_date); 
        $item->description = "<![CDATA[ $html ]]>";
        $feed->addItem($item);
    }
    echo $feed->serve();
    ---------------------------------------------------------------- */
}