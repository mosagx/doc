<?php

class CSV
{
    private $_data;

    private $_name;

    private $_path = __DIR__.'/../data/';

    private $_fp;

    private $spl_object;

    /**
     * 打开文件句柄
     */
    public function start($filename)
    {
        $this->_name = $filename;
        $this->_fp   = fopen($this->_getFile(), 'a');
    }

    
    /**
     * 写入文件
     */
    public function write($data)
    {
        if (count($data) == count($data, 1)) {
            array_walk($data, function (&$value) {
                $value = iconv('utf-8', 'gbk', $value);
            });
            fputcsv($this->_fp, $data);
        } else {
            foreach ($data as $item) {
                $ins = array_values($item);
                array_walk($ins, function (&$value) {
                    $value = iconv('utf-8', 'gbk', $value);
                });
                fputcsv($this->_fp, $ins);
            }
        }
        unset($data);
    }


    /**
     * 关闭文件句柄
     */
    public function close()
    {
        fclose($this->_fp);
    }


    /**
     * 读取文件句柄
     */
    public function read()
    {
        $this->spl_object = new SplFileObject($this->_getFile(), 'rb');
    }


    /**
     * 获取表格行数
     */
    public function getLine()
    {
        $this->spl_object->seek(filesize($this->_getFile()));

        return $this->spl_object->key();
    }


    /**
     * 分片获取数据
     *
     * @param integer $lines 获取条数
     * @param integer $offset 起始位置
     * @return array
     */
    public function getContent($lines, $offset = 0)
    {
        if (!$fp = fopen($this->_getFile(), 'r')) {
            return false;
        }
        $i = $j = 0;
        while (false !== ($line = fgets($fp))) {
            if ($i++ < $offset) {
                continue;
            }
            break;
        }

        $data = [];
        while (($j < $lines) && !feof($fp)) {
            $item = fgetcsv($fp);
            if (is_array($item)) {
                array_walk($item, function (&$value) {
                    $value = iconv('gbk', 'utf-8', $value);
                });
                $data[] = $item;
            }
            ++$j;
        }
        fclose($fp);

        return $data;
    }


    /**
     * 获取文件路径
     */
    private function _getFile()
    {
        $path = $this->_path.$this->_name.'.csv';

        return $path;
    }
}
